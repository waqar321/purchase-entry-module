<?php

namespace App\Livewire;

use App\Models\Purchase;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PurchaseShow extends Component
{
    use AuthorizesRequests;

    public Purchase $purchase;

    public function mount(Purchase $purchase): void
    {
        $this->authorize('purchase_view');
        $this->purchase = $purchase->load('items.item', 'items.brand');
    }

    public function render()
    {
        return view('livewire.purchase-show');
    }
}
