<?php

namespace App\Livewire;

use App\Models\Purchase;
use App\Services\LegacyPurchaseMigrator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PurchaseIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public ?string $migrationMessage = null;

    public function delete(int $purchaseId): void
    {
        $purchase = Purchase::findOrFail($purchaseId);
        $this->authorize('purchase_delete');

        $purchase->delete();

        session()->flash('status', 'Purchase deleted successfully.');
    }

    public function runLegacyMigration(LegacyPurchaseMigrator $migrator): void
    {
        $this->authorize('legacy_migrate');

        $legacyPurchases = [
            [
                'item_name' => 'Sugar',
                'brand_name' => 'ABC',
                'qty' => 10,
                'price' => 100,
            ],
        ];

        $result = $migrator->migrate($legacyPurchases);

        $this->migrationMessage = "Legacy migration complete. Migrated: {$result['migrated']}, Skipped: {$result['skipped']}.";
    }

    public function render()
    {
        return view('livewire.purchase-index', [
            'purchases' => Purchase::with('items.item', 'items.brand')
                ->latest()
                ->paginate(10),
        ]);
    }
}
