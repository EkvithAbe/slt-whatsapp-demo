<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\SltWhatsappClient;
use Illuminate\Console\Command;

class WhatsappSyncContacts extends Command
{
    protected $signature = 'whatsapp:sync-contacts {--limit=5}';
    protected $description = 'Sync the most-recent active mobile numbers into contacts table';

    public function handle(SltWhatsappClient $client): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $mobiles = $client->getRecentActiveMobiles($limit);

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
}
