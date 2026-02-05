<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use App\Services\SltWhatsappClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatApiController extends Controller
{
    public function messages(Contact $contact, Request $request)
    {
        $limit = (int) $request->query('limit', 200);

        $query = Message::where('contact_id', $contact->id);
        if ($limit > 0) {
            // Pull latest N messages, then sort ascending for display.
            $collection = $query->orderBy('sent_at', 'desc')
                ->limit($limit)
                ->get()
                ->sortBy('sent_at')
                ->values();
        } else {
            // limit=0 means "all messages"
            $collection = $query->orderBy('sent_at')->get();
        }

        $messages = $collection->map(fn($m) => [
            'id' => $m->id,
            'uuid' => $m->uuid,
            'direction' => $m->direction,
            'body' => $m->body,
            'sent_at' => optional($m->sent_at)->toIso8601String(),
        ]);

        return response()->json(['messages' => $messages]);
    }

    public function sync(Contact $contact)
    {
        // lightweight manual sync just for this contact
        \Artisan::call('whatsapp:sync', [
            '--contact_id' => $contact->id,
            '--limit' => 30,
        ]);

        return response()->json(['ok' => true]);
    }

    public function syncAll(Request $request)
    {
        $limit = (int) $request->input('limit', 30);
        $limit = max(1, min(200, $limit));

        \Artisan::call('whatsapp:sync', [
            '--limit' => $limit,
        ]);

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

        // reply needs a uuid. We'll reply to the latest inbound message.
        $replyTo = Message::where('contact_id', $contact->id)
            ->where('direction', 'in')
            ->latest('sent_at')
            ->first();

        if (!$replyTo?->uuid) {
            return response()->json([
                'ok' => false,
                'error' => 'No inbound message uuid found to reply to. Sync first.'
            ], 422);
        }

        $client->reply($replyTo->uuid, $contact->mobile, $data['message']);

        // Store outbound message immediately (so it shows in demo UI)
        Message::create([
            'contact_id' => $contact->id,
            'uuid' => 'out_' . uniqid(),
            'direction' => 'out',
            'body' => $data['message'],
            'sent_at' => now(),
            'raw' => ['reply_to_uuid' => $replyTo->uuid],
        ]);

        return response()->json(['ok' => true]);
    }
}
