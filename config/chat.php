<?php

return [
    // How long a chat lock is valid without a heartbeat (seconds)
    'lock_ttl_seconds' => (int) env('CHAT_LOCK_TTL_SECONDS', 1800),
    // How many chats to show in the sidebar list
    'list_limit' => (int) env('CHAT_LIST_LIMIT', 40),
    // How many recent mobiles to request per sync run
    'sync_recent_limit' => (int) env('CHAT_SYNC_RECENT_LIMIT', 40),
    // Safety cap for incoming sync limit values
    'sync_recent_max_limit' => (int) env('CHAT_SYNC_RECENT_MAX_LIMIT', 200),
    // A recurring error is marked active if seen within this window (minutes)
    'error_active_window_minutes' => (int) env('CHAT_ERROR_ACTIVE_WINDOW_MINUTES', 5),
];
