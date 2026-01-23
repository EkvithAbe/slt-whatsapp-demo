<?php

namespace App\Http\Controllers;

use App\Models\ApiLog;

class LogController extends Controller
{
    public function index()
    {
        // Simple rule: only admins can view logs (set is_admin=1 for your user)
        abort_unless(auth()->user()?->is_admin, 403);

        $logs = ApiLog::latest()->paginate(30);
        return view('logs.index', compact('logs'));
    }
}

