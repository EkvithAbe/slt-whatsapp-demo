<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Support\Str;
use App\Services\SltWhatsappClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ChatApiController extends Controller
{
    public function messages(Contact $contact, SltWhatsappClient $client)
    {
        // Each API row can now split into two bubbles (incoming + reply).
        // Keep ~5 recent conversation pairs visible in UI.
        $limit = 10;
        // Pull a larger window to keep ordering stable before slicing.
        $apiLimit = 50;

        try {
            $payload = $client->getMessages($contact->mobile, $apiLimit);
            $apiMessages = $this->normalizeMessages($payload);
        } catch (\Throwable $e) {
            $this->logChatApiError('messages.fetch_failed', $contact, $e, [
                'mobile' => $contact->mobile,
                'api_limit' => $apiLimit,
            ]);
            return response()->json([
                'messages' => [],
                'error' => 'Failed to fetch messages from SLT API.',
            ], 502);
        }

        $localOutgoing = $contact->messages()
            ->where('direction', 'out')
            ->whereNotNull('sent_at')
            ->orderBy('sent_at')
            ->limit(200)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => 'local_' . $m->id,
                    'uuid' => (string) ($m->uuid ?: ('local_' . $m->id)),
                    'direction' => 'out',
                    'body' => (string) ($m->body ?? ''),
                    'sent_at' => $m->sent_at
                        ? $m->sent_at->setTimezone((string) config('app.timezone', 'Asia/Colombo'))->toIso8601String()
                        : null,
                    'time_hint' => null,
                ];
            })
            ->filter(fn (array $m) => trim((string) ($m['body'] ?? '')) !== '')
            ->values()
            ->all();

        $messages = array_values(array_merge($apiMessages, $localOutgoing));
        foreach ($messages as $idx => &$m) {
            $m['_seq'] = $idx + 1;
        }
        unset($m);

        usort($messages, function (array $a, array $b) {
            $at = $a['sent_at'] ?? null;
            $bt = $b['sent_at'] ?? null;
            if (!$at && !$bt) {
                return ($a['_seq'] ?? 0) <=> ($b['_seq'] ?? 0);
            }
            if (!$at) {
                return -1;
            }
            if (!$bt) {
                return 1;
            }
            $cmp = strcmp((string) $at, (string) $bt);
            if ($cmp !== 0) {
                return $cmp;
            }
            return ($a['_seq'] ?? 0) <=> ($b['_seq'] ?? 0);
        });

        $messages = array_map(function (array $m) {
            unset($m['_seq']);
            return $m;
        }, $messages);

        $messages = array_slice($messages, -$limit);

        return response()->json(['messages' => $messages]);
    }

    public function sync(Contact $contact)
    {
        // Kept for frontend compatibility. Messages are fetched live from API.
        $contact->update(['last_synced_at' => now()]);
        return response()->json(['ok' => true]);
    }

    public function syncAll(Request $request)
    {
        // Kept for frontend compatibility. No DB message sync is performed.
        return response()->json(['ok' => true]);
    }

    public function markRead(Contact $contact)
    {
        $contact->update(['last_read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    public function send(Contact $contact, Request $request, SltWhatsappClient $client)
    {
        // Enforce chat lock (prevents multiple admins replying at the same time)
        $lockOk = DB::transaction(function () use ($contact) {
            /** @var Contact $c */
            $c = Contact::whereKey($contact->id)->lockForUpdate()->firstOrFail();

            // Clear stale locks
            if ($c->locked_by_user_id && $c->isLockExpired()) {
                $c->locked_by_user_id = null;
                $c->locked_at = null;
                $c->save();
            }

            // If locked by someone else, reject
            if ($c->locked_by_user_id && $c->locked_by_user_id !== auth()->id()) {
                $c->load('lockedBy');
                return ['ok' => false, 'locked_by' => $c->lockedBy?->name];
            }

            // If unlocked or locked by me, refresh lock
            $c->locked_by_user_id = auth()->id();
            $c->locked_at = now();
            $c->save();
            return ['ok' => true];
        });

        if (!$lockOk['ok']) {
            return response()->json([
                'ok' => false,
                'error' => 'Chat is locked by ' . ($lockOk['locked_by'] ?? 'another admin') . '. Please wait.'
            ], 423);
        }

        $data = $request->validate([
            'message' => ['required','string','max:2000'],
        ]);

        // reply needs a uuid. Find the latest inbound uuid from API data.
        try {
            $replyToUuid = $this->latestInboundUuid($client, $contact->mobile);
        } catch (\Throwable $e) {
            $this->logChatApiError('messages.inbound_uuid_failed', $contact, $e, [
                'mobile' => $contact->mobile,
            ]);
            return response()->json([
                'ok' => false,
                'error' => 'Failed to load inbound messages from SLT API.',
            ], 502);
        }

        if (!$replyToUuid) {
            return response()->json([
                'ok' => false,
                'error' => 'No inbound message uuid found from API for this contact.'
            ], 422);
        }

        try {
            $replyPayload = $client->reply($replyToUuid, $contact->mobile, $data['message']);
        } catch (\Throwable $e) {
            $this->logChatApiError('messages.reply_failed', $contact, $e, [
                'mobile' => $contact->mobile,
                'reply_to_uuid' => $replyToUuid,
            ]);
            return response()->json([
                'ok' => false,
                'error' => 'Failed to send reply via SLT API.',
            ], 502);
        }

        $replyError = $this->extractReplyError($replyPayload);
        if ($replyError) {
            return response()->json([
                'ok' => false,
                'error' => $replyError,
            ], 422);
        }

        $this->storeLocalOutgoingMessage($contact, $data['message'], $replyPayload);

        return response()->json(['ok' => true]);
    }

    private function latestInboundUuid(SltWhatsappClient $client, string $mobile): ?string
    {
        $payload = $client->getMessages($mobile, 100);
        $messages = $this->normalizeMessages($payload);

        for ($i = count($messages) - 1; $i >= 0; $i--) {
            $m = $messages[$i];
            if (($m['direction'] ?? 'in') === 'in' && !empty($m['uuid'])) {
                return (string) $m['uuid'];
            }
        }

        return null;
    }

    private function extractReplyError(array $payload): ?string
    {
        $status = $payload['status'] ?? null;
        if (is_string($status)) {
            $text = trim($status);
            if ($text !== '' && str_starts_with(strtolower($text), 'error')) {
                return $text;
            }
        }

        $error = $payload['error'] ?? null;
        if (is_string($error) && trim($error) !== '') {
            return trim($error);
        }

        if (is_array($error)) {
            $message = trim((string) ($error['message'] ?? ''));
            if ($message !== '') {
                return $message;
            }
        }

        return null;
    }

    private function storeLocalOutgoingMessage(Contact $contact, string $body, array $replyPayload): void
    {
        $uuid = $this->extractOutgoingUuid($replyPayload) ?: (string) Str::uuid();

        $contact->messages()->updateOrCreate(
            ['uuid' => $uuid],
            [
                'direction' => 'out',
                'body' => $body,
                'sent_at' => now(),
                'raw' => $replyPayload,
            ]
        );
        $contact->touch();
    }

    private function extractOutgoingUuid(array $payload): ?string
    {
        $candidates = [
            $payload['status']['messages'][0]['id'] ?? null,
            $payload['message_id'] ?? null,
            $payload['reply_id'] ?? null,
            $payload['id'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $value = trim((string) ($candidate ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function normalizeMessages(array $payload): array
    {
        $items = $payload['messages']
            ?? $payload['data']
            ?? $payload['result']
            ?? $payload;

        if (!is_array($items)) {
            return [];
        }

        // Some APIs return one message object directly.
        if ($this->looksLikeMessage($items)) {
            $items = [$items];
        }

        $messages = [];
        $seq = 0;

        $appendMessage = function (
            string $id,
            string $uuid,
            string $body,
            string $direction,
            ?string $sentAt,
            ?string $timeHint
        ) use (&$messages, &$seq): void {
            $seq++;
            $messages[] = [
                'id' => $id,
                'uuid' => $uuid,
                'direction' => $direction,
                'body' => trim($body),
                'sent_at' => $sentAt,
                'time_hint' => $timeHint,
                '_seq' => $seq,
            ];
        };

        $itemIndex = 0;
        foreach ($items as $m) {
            if (!is_array($m)) {
                continue;
            }

            $itemIndex++;
            $uuid = (string) ($m['uuid'] ?? $m['message_id'] ?? $m['id'] ?? '');
            $idSource = $m['id'] ?? ($uuid !== '' ? $uuid : "api_{$itemIndex}");
            $baseId = (string) $idSource;
            $primaryBody = trim((string) ($m['message'] ?? $m['text'] ?? $m['body'] ?? ''));
            $replyBody = trim((string) ($m['reply'] ?? $m['replymessage'] ?? ''));
            $sentAt = $this->extractIsoTimestamp($m);
            $timeHint = $this->extractTimeHint($m);
            $direction = $this->normalizeDirection($m);
            $hasDirectionalField = $this->hasDirectionalField($m);
            $messageUuid = $uuid !== '' ? $uuid : $baseId;

            // SLT commonly returns one row with both "message" (incoming) and "reply" (outgoing).
            // Split those into two chat bubbles to render both sides.
            if ($primaryBody !== '' && $replyBody !== '' && !$hasDirectionalField) {
                $appendMessage("{$baseId}_in", $messageUuid, $primaryBody, 'in', $sentAt, $timeHint);
                $appendMessage("{$baseId}_out", $messageUuid, $replyBody, 'out', $sentAt, $timeHint);
                continue;
            }

            if ($primaryBody !== '') {
                $primaryDirection = $hasDirectionalField ? $direction : 'in';
                $primaryId = $replyBody !== '' ? "{$baseId}_in" : $baseId;
                $appendMessage($primaryId, $messageUuid, $primaryBody, $primaryDirection, $sentAt, $timeHint);
            }

            if ($replyBody !== '') {
                $replyDirection = ($hasDirectionalField && $primaryBody === '') ? $direction : 'out';
                $replyId = $primaryBody !== '' ? "{$baseId}_out" : $baseId;
                $appendMessage($replyId, $messageUuid, $replyBody, $replyDirection, $sentAt, $timeHint);
            }
        }

        usort($messages, function (array $a, array $b) {
            $at = $a['sent_at'] ?? null;
            $bt = $b['sent_at'] ?? null;
            if (!$at && !$bt) {
                return ($a['_seq'] ?? 0) <=> ($b['_seq'] ?? 0);
            }
            if (!$at) {
                return -1;
            }
            if (!$bt) {
                return 1;
            }
            $cmp = strcmp($at, $bt);
            if ($cmp !== 0) {
                return $cmp;
            }
            return ($a['_seq'] ?? 0) <=> ($b['_seq'] ?? 0);
        });

        return array_map(function (array $m) {
            unset($m['_seq']);
            return $m;
        }, $messages);
    }

    private function normalizeDirection(array $m): string
    {
        if (isset($m['from_me'])) {
            return filter_var($m['from_me'], FILTER_VALIDATE_BOOLEAN) ? 'out' : 'in';
        }

        if (isset($m['fromMe'])) {
            return filter_var($m['fromMe'], FILTER_VALIDATE_BOOLEAN) ? 'out' : 'in';
        }

        $raw = strtolower((string) ($m['direction'] ?? $m['type'] ?? ''));
        if (in_array($raw, ['out', 'outgoing', 'sent', 'agent', 'reply'], true)) {
            return 'out';
        }
        if (in_array($raw, ['in', 'incoming', 'received', 'customer'], true)) {
            return 'in';
        }

        return 'in';
    }

    private function hasDirectionalField(array $m): bool
    {
        if (array_key_exists('from_me', $m) || array_key_exists('fromMe', $m)) {
            return true;
        }

        $raw = strtolower((string) ($m['direction'] ?? $m['type'] ?? ''));
        if ($raw === '') {
            return false;
        }

        return in_array($raw, [
            'out',
            'outgoing',
            'sent',
            'agent',
            'reply',
            'in',
            'incoming',
            'received',
            'customer',
        ], true);
    }

    private function extractIsoTimestamp(array $m): ?string
    {
        $tz = (string) config('app.timezone', 'Asia/Colombo');

        $ts = $m['timestamp']
            ?? $m['time']
            ?? $m['sent_at']
            ?? $m['created_at']
            ?? null;

        if (!$ts) {
            return null;
        }

        try {
            if (is_numeric($ts)) {
                $n = (int) $ts;
                if ($n > 9999999999) {
                    return Carbon::createFromTimestampMs($n, 'UTC')->setTimezone($tz)->toIso8601String();
                }
                return Carbon::createFromTimestamp($n, 'UTC')->setTimezone($tz)->toIso8601String();
            }
            $raw = trim((string) $ts);
            if ($this->isTimeOnlyTimestamp($raw)) {
                return null;
            }
            $hasOffset = (bool) preg_match('/(Z|[+\-]\d{2}:?\d{2})$/i', $raw);
            $parsed = $hasOffset
                ? Carbon::parse($raw)
                : Carbon::parse($raw, 'UTC');

            return $parsed->setTimezone($tz)->toIso8601String();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function extractTimeHint(array $m): ?string
    {
        $ts = $m['timestamp']
            ?? $m['time']
            ?? $m['sent_at']
            ?? $m['created_at']
            ?? null;

        if (!$ts || is_numeric($ts)) {
            return null;
        }

        $raw = trim((string) $ts);
        if (!$this->isTimeOnlyTimestamp($raw)) {
            return null;
        }

        try {
            return Carbon::parse($raw)->format('H:i');
        } catch (\Throwable $e) {
            return $raw;
        }
    }

    private function isTimeOnlyTimestamp(string $value): bool
    {
        $raw = trim($value);
        if ($raw === '') {
            return false;
        }

        return (bool) preg_match('/^\d{1,2}:\d{2}(:\d{2})?\s?(am|pm)?$/i', $raw);
    }

    private function looksLikeMessage(array $item): bool
    {
        return array_key_exists('uuid', $item)
            || array_key_exists('message', $item)
            || array_key_exists('text', $item)
            || array_key_exists('body', $item);
    }

    private function logChatApiError(string $event, Contact $contact, \Throwable $e, array $extra = []): void
    {
        Log::error("Chat API error: {$event}", array_merge([
            'contact_id' => $contact->id,
            'contact_mobile' => $contact->mobile,
            'user_id' => auth()->id(),
            'exception_class' => get_class($e),
            'exception_message' => $e->getMessage(),
        ], $extra));
    }
}
