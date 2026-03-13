<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    private const SYNC_RECENT_FALLBACK_LIMIT = 40;
    private const SYNC_RECENT_MAX_LIMIT = 200;

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['nullable','string','max:80'],
            'mobile' => ['required','string','max:20'],
        ]);

        $data['mobile'] = preg_replace('/\D+/', '', $data['mobile']);

        // normalize to 94xxxxxxxxx if user enters 07xxxxxxxx
        if (str_starts_with($data['mobile'], '0')) {
            $data['mobile'] = '94' . ltrim($data['mobile'], '0');
        }

        Contact::updateOrCreate(
            ['mobile' => $data['mobile']],
            ['name' => $data['name'] ?: $data['mobile']]
        );

        return redirect()->route('chats.index')->with('status', 'Contact saved.');
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'name' => ['nullable','string','max:80'],
        ]);

        $contact->update([
            'name' => $data['name'] ?: $contact->mobile
        ]);

        return redirect()->route('chats.show', $contact)->with('status', 'Contact updated.');
    }

    /**
     * Pull the last active mobiles from SLT API and upsert them into contacts.
     */
    public function syncRecent(Request $request)
    {
        $defaultLimit = (int) config('chat.sync_recent_limit', self::SYNC_RECENT_FALLBACK_LIMIT);
        $maxLimit = (int) config('chat.sync_recent_max_limit', self::SYNC_RECENT_MAX_LIMIT);
        $maxLimit = max(1, $maxLimit);

        $limit = (int) ($request->input('limit') ?? $defaultLimit);
        $limit = max(1, min($maxLimit, $limit));

        try {
            Artisan::call('whatsapp:sync-contacts', [
                '--limit' => $limit,
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->route('chats.index')
                ->with('error', 'Recent contacts sync failed. Please try again in a moment.');
        }

        $output = strtolower(trim(Artisan::output()));
        if (str_contains($output, 'skipped')) {
            return redirect()
                ->route('chats.index')
                ->with('error', 'Recent contacts sync is currently unavailable.');
        }

        return redirect()
            ->route('chats.index')
            ->with('status', "Synced last {$limit} recent mobiles.");
    }
}
