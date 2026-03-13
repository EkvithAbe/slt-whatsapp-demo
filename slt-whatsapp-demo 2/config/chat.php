<?php

return [
    // How long a chat lock is valid without a heartbeat (seconds)
    'lock_ttl_seconds' => (int) env('CHAT_LOCK_TTL_SECONDS', 1800),
];
