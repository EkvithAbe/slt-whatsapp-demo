<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use App\Services\SltWhatsappClient;
use Illuminate\Http\Request;

class ChatApiController extends Controller
{
    public function messages(Contact $contact, Request $request)
    {
        $limit = (int) $request->query('limit', 50);

        $messages = Message::where('contact_id', $contact->id)
            ->orderBy('sent_at')
            ->limit($limit)
            ->get()
            ->map(fn($m) => [
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

    public function send(Contact $contact, Request $request, SltWhatsappClient $client)
    {
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

