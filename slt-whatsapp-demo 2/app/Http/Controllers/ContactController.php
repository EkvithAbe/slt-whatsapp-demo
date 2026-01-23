<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
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
}

