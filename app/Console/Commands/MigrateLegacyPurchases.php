<?php

namespace App\Console\Commands;

use App\Services\LegacyPurchaseMigrator;
use Illuminate\Console\Command;

class MigrateLegacyPurchases extends Command
{
    protected $signature = 'purchases:migrate-legacy';

    protected $description = 'Migrate legacy purchase data into normalized tables (idempotent)';

    public function handle(LegacyPurchaseMigrator $migrator): int
    {
        $legacyPurchases = [
            [
                'item_name' => 'Sugar',
                'brand_name' => 'ABC',
                'qty' => 10,
                'price' => 100,
            ],
        ];

        $result = $migrator->migrate($legacyPurchases);

        $this->info("Legacy migration complete. Migrated: {$result['migrated']}, Skipped: {$result['skipped']}.");

        return self::SUCCESS;
    }
}
