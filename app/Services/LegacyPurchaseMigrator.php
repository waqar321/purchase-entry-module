<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Item;
use App\Models\LegacyPurchaseMigration;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class LegacyPurchaseMigrator
{
    /**
     * @param  array<int, array{item_name: string, brand_name: string, qty: int|float, price: int|float}>  $legacyPurchases
     * @return array{migrated: int, skipped: int}
     */
    public function migrate(array $legacyPurchases): array
    {
        $migrated = 0;
        $skipped = 0;

        DB::transaction(function () use ($legacyPurchases, &$migrated, &$skipped) {
            foreach ($legacyPurchases as $row) {
                $legacyKey = $this->buildLegacyKey($row);

                if (LegacyPurchaseMigration::where('legacy_key', $legacyKey)->exists()) {
                    $skipped++;

                    continue;
                }

                $item = Item::firstOrCreate(
                    ['name' => trim($row['item_name'])],
                );

                $brand = Brand::firstOrCreate(
                    ['name' => trim($row['brand_name'])],
                );

                $purchase = Purchase::create(['total' => 0]);

                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                    'brand_id' => $brand->id,
                    'qty' => (int) $row['qty'],
                    'price' => (float) $row['price'],
                ]);

                $purchase->recalculateTotal();

                LegacyPurchaseMigration::create([
                    'legacy_key' => $legacyKey,
                    'purchase_id' => $purchase->id,
                    'purchase_item_id' => $purchaseItem->id,
                ]);

                $migrated++;
            }
        });

        return compact('migrated', 'skipped');
    }

    /**
     * @param  array{item_name: string, brand_name: string, qty: int|float, price: int|float}  $row
     */
    private function buildLegacyKey(array $row): string
    {
        return hash('sha256', implode('|', [
            strtolower(trim($row['item_name'])),
            strtolower(trim($row['brand_name'])),
            (int) $row['qty'],
            number_format((float) $row['price'], 2, '.', ''),
        ]));
    }
}
