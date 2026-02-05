<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatLockController extends Controller
{
    public function status(Contact $contact)
    {
        $contact->load('lockedBy');

        // Auto-clear stale locks
        if ($contact->locked_by_user_id && $contact->isLockExpired()) {
            $contact->locked_by_user_id = null;
            $contact->locked_at = null;
            $contact->save();
            $contact->load('lockedBy');
        }

        $locked = (bool) $contact->locked_by_user_id;
        $lockedBy = $contact->lockedBy?->name;
        $lockedById = $contact->locked_by_user_id;

        return response()->json([
            'locked' => $locked,
            'locked_by' => $lockedBy,
            'locked_by_id' => $lockedById,
            'locked_by_me' => $lockedById ? $lockedById === auth()->id() : false,
            'ttl_seconds' => (int) config('chat.lock_ttl_seconds', 120),
            'locked_at' => optional($contact->locked_at)->toIso8601String(),
        ]);
    }

    /**
     * Acquire or refresh a lock for this contact.
     * - If unlocked or stale: assigns lock to current user
     * - If locked by me: refreshes timestamp
     * - If locked by another (and not stale): returns locked info
     */
    public function acquire(Contact $contact)
    {
        $userId = auth()->id();

        $result = DB::transaction(function () use ($contact, $userId) {
            /** @var Contact $c */
            $c = Contact::whereKey($contact->id)->lockForUpdate()->firstOrFail();
            $c->load('lockedBy');

            // Clear stale
            if ($c->locked_by_user_id && $c->isLockExpired()) {
                $c->locked_by_user_id = null;
                $c->locked_at = null;
            }

            if (!$c->locked_by_user_id || $c->locked_by_user_id === $userId) {
                $c->locked_by_user_id = $userId;
                $c->locked_at = now();
                $c->save();
                $c->load('lockedBy');
            }

            return $c;
        });

        return response()->json([
            'locked' => (bool) $result->locked_by_user_id,
            'locked_by' => $result->lockedBy?->name,
            'locked_by_id' => $result->locked_by_user_id,
            'locked_by_me' => $result->locked_by_user_id ? $result->locked_by_user_id === auth()->id() : false,
            'ttl_seconds' => (int) config('chat.lock_ttl_seconds', 120),
            'locked_at' => optional($result->locked_at)->toIso8601String(),
        ]);
    }

    public function release(Contact $contact)
    {
        $userId = auth()->id();

        DB::transaction(function () use ($contact, $userId) {
            /** @var Contact $c */
            $c = Contact::whereKey($contact->id)->lockForUpdate()->firstOrFail();
            if ($c->locked_by_user_id === $userId) {
                $c->locked_by_user_id = null;
                $c->locked_at = null;
                $c->save();
            }
        });

        return response()->json(['ok' => true]);
    }
}
