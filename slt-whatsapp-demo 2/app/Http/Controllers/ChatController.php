<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private function contactsQuery()
    {
        return Contact::with('lastMessage')
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('direction', 'in')
                    ->where(function ($q) {
                        $q->whereNull('contacts.last_read_at')
                            ->orWhereColumn('messages.created_at', '>', 'contacts.last_read_at');
                    });
            }])
            ->orderByDesc(
                Message::select('sent_at')
                    ->whereColumn('messages.contact_id', 'contacts.id')
                    ->latest('sent_at')
                    ->take(1)
            );
    }

    public function index()
    {
        $contacts = $this->contactsQuery()->get();

        return view('chats.index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        $contacts = $this->contactsQuery()->get();
        return view('chats.show', compact('contacts', 'contact'));
    }

    public function list(Request $request)
    {
        $contacts = $this->contactsQuery()->get();
        $activeContactId = $request->query('active_contact_id');
        $showLock = $request->boolean('show_lock', false);
        $showActive = $request->boolean('show_active', false);
        $showPreview = $request->boolean('show_preview', false);
        return view('chats.partials.list-items', compact(
            'contacts',
            'activeContactId',
            'showLock',
            'showActive',
            'showPreview'
        ));
    }
}
