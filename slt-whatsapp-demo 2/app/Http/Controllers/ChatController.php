<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ChatController extends Controller
{
    public function index()
    {
        $contacts = Contact::with('lastMessage')
            ->orderByDesc(
                \App\Models\Message::select('sent_at')
                    ->whereColumn('messages.contact_id', 'contacts.id')
                    ->latest('sent_at')
                    ->take(1)
            )
            ->get();

        return view('chats.index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        $contacts = Contact::with('lastMessage')->get();
        return view('chats.show', compact('contacts', 'contact'));
    }
}
