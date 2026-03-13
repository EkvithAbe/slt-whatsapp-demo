<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\SltWhatsappClient;
use Illuminate\Console\Command;

class WhatsappSyncContacts extends Command
{
    protected $signature = 'whatsapp:sync-contacts {--limit=40}';
    protected $description = 'Sync the most-recent active mobile numbers into contacts table';

    public function handle(SltWhatsappClient $client): int
    {
        $limit = max(1, (int) $this->option('limit'));

        try {
            $mobiles = $client->getRecentActiveMobiles($limit);
        } catch (\Throwable $e) {
            $this->warn('Recent contacts sync skipped: ' . $this->shortError($e));
            return self::SUCCESS;
        }

        if (empty($mobiles)) {
            $this->warn('No recent mobiles returned by API.');
            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;

        foreach ($mobiles as $m) {
            $contact = Contact::where('mobile', $m)->first();

            if (!$contact) {
                Contact::create([
                    'mobile' => $m,
                    'name' => $m,
                ]);
                $created++;
                continue;
            }

            // Keep existing name if it's meaningful; otherwise default to mobile.
            $name = $contact->name;
            if (!$name || $name === $contact->mobile) {
                $contact->name = $m;
            }
            $contact->touch();
            $contact->save();
            $updated++;
        }

        $this->info("Synced contacts. created={$created}, updated={$updated}, total=" . count($mobiles));
        return self::SUCCESS;
    }

    private function shortError(\Throwable $e): string
    {
        $message = trim($e->getMessage());
        if ($message !== '') {
            return $message;
        }

        return class_basename($e);
    }
}
