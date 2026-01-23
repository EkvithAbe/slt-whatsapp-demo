<?php

namespace App\Services;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SltWhatsappClient
{
    private string $base;
    private string $username;
    private string $password;
    private string $phoneNumberId;

    public function __construct()
    {
        $cfg = config('services.slt_whatsapp');
        $this->base = rtrim($cfg['base'], '/');
        $this->username = $cfg['username'];
        $this->password = $cfg['password'];
        $this->phoneNumberId = $cfg['phone_number_id'];
    }

    public function phoneNumberId(): string
    {
        return $this->phoneNumberId;
    }

    public function getToken(): string
    {
        return Cache::remember('slt_whatsapp_token', now()->addMinutes(50), function () {
            return $this->login();
        });
    }

    public function login(): string
    {
        $url = "{$this->base}/login.php";

        $start = microtime(true);
        $resp = Http::timeout(15)->asJson()->post($url, [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        $duration = (int) round((microtime(true) - $start) * 1000);

        $data = $resp->json();
        $token = $data['token'] ?? $data['access_token'] ?? $data['data']['token'] ?? null;

        ApiLog::create([
            'user_id' => auth()->id(),
            'endpoint' => '/login.php',
            'method' => 'POST',
            'status' => $resp->status(),
            'duration_ms' => $duration,
            'request' => ['username' => $this->username, 'password' => '***'],
            'response' => $this->safeResponse($data),
        ]);

        if (!$resp->successful() || !$token) {
            throw new \RuntimeException("SLT login failed (status {$resp->status()}).");
        }

        return $token;
    }

    public function getMessages(string $mobile, int $limit = 20): array
    {
        return $this->authedPost('/getMessages.php', [
            'mobile' => $mobile,
            'limit' => $limit,
        ]);
    }

    public function reply(string $uuid, string $mobile, string $replyMessage): array
    {
        return $this->authedPost('/reply.php', [
            'uuid' => $uuid,
            'phone_number_id' => $this->phoneNumberId(),
            'mobile' => $mobile,
            'replymessage' => $replyMessage,
        ]);
    }

    private function authedPost(string $endpoint, array $payload): array
    {
        $token = $this->getToken();
        $url = "{$this->base}{$endpoint}";

        $start = microtime(true);
        $resp = Http::timeout(20)
            ->withToken($token)
            ->asJson()
            ->post($url, $payload);

        // If token expired, retry once
        if ($resp->status() === 401) {
            Cache::forget('slt_whatsapp_token');
            $token = $this->getToken();

            $start = microtime(true);
            $resp = Http::timeout(20)
                ->withToken($token)
                ->asJson()
                ->post($url, $payload);
        }

        $duration = (int) round((microtime(true) - $start) * 1000);

        ApiLog::create([
            'user_id' => auth()->id(),
            'endpoint' => $endpoint,
            'method' => 'POST',
            'status' => $resp->status(),
            'duration_ms' => $duration,
            'request' => $this->safeRequest($payload),
            'response' => $this->safeResponse($resp->json()),
        ]);

        if (!$resp->successful()) {
            throw new \RuntimeException("SLT API failed {$endpoint} (status {$resp->status()}).");
        }

        return $resp->json() ?? [];
    }

    private function safeRequest(array $payload): array
    {
        // Don’t log anything sensitive if you add more fields later
        return $payload;
    }

    private function safeResponse($data)
    {
        // Avoid logging huge payloads
        if (is_array($data) && count($data) > 2000) {
            return ['_truncated' => true];
        }
        return $data;
    }
}
