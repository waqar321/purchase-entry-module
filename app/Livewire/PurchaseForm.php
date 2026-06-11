<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PurchaseForm extends Component
{
    use AuthorizesRequests;

    public ?Purchase $purchase = null;

    /** @var array<int, array{item_id: string, brand_id: string, qty: string, price: string}> */
    public array $rows = [];

    public function mount(?Purchase $purchase = null): void
    {
        if ($purchase) {
            $this->authorize('purchase_edit');
            $this->purchase = $purchase;

            $this->rows = $purchase->items->map(fn (PurchaseItem $item) => [
                'item_id' => (string) $item->item_id,
                'brand_id' => (string) $item->brand_id,
                'qty' => (string) $item->qty,
                'price' => (string) $item->price,
            ])->values()->all();
        } else {
            $this->authorize('purchase_create');
            $this->addRow();
        }
    }

    public function addRow(): void
    {
        $this->rows[] = [
            'item_id' => '',
            'brand_id' => '',
            'qty' => '',
            'price' => '',
        ];
    }

    public function removeRow(int $index): void
    {
        if (count($this->rows) <= 1) {
            return;
        }

        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function updatedRows(): void
    {
        $this->validateRows(live: true);
    }

    public function save(): void
    {
        $this->validateRows(live: false);

        DB::transaction(function () {
            if ($this->purchase) {
                $purchase = $this->purchase;
                $purchase->items()->delete();
            } else {
                $purchase = Purchase::create(['total' => 0]);
            }

            foreach ($this->rows as $row) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => (int) $row['item_id'],
                    'brand_id' => (int) $row['brand_id'],
                    'qty' => (int) $row['qty'],
                    'price' => (float) $row['price'],
                ]);
            }

            $purchase->recalculateTotal();
        });

        session()->flash('status', $this->purchase ? 'Purchase updated successfully.' : 'Purchase created successfully.');

        $this->redirect(route('purchases.index'), navigate: true);
    }

    private function validateRows(bool $live = false): void
    {
        $rules = [
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.item_id' => ['required', Rule::exists('items', 'id')],
            'rows.*.brand_id' => ['required', Rule::exists('brands', 'id')],
            'rows.*.qty' => ['required', 'integer', 'min:1'],
            'rows.*.price' => ['required', 'numeric', 'min:0.01'],
        ];

        $messages = [
            'rows.*.item_id.required' => 'Please select an item.',
            'rows.*.brand_id.required' => 'Please select a brand.',
            'rows.*.qty.required' => 'Quantity is required.',
            'rows.*.qty.min' => 'Quantity must be at least 1.',
            'rows.*.price.required' => 'Price is required.',
            'rows.*.price.min' => 'Price must be greater than zero.',
        ];

        if ($live) {
            $this->resetErrorBag();

            foreach ($this->rows as $index => $row) {
                if ($row['item_id'] !== '') {
                    $this->validate([
                        "rows.{$index}.item_id" => ['required', Rule::exists('items', 'id')],
                    ], $messages);
                }

                if ($row['brand_id'] !== '') {
                    $this->validate([
                        "rows.{$index}.brand_id" => ['required', Rule::exists('brands', 'id')],
                    ], $messages);
                }

                if ($row['qty'] !== '') {
                    $this->validate([
                        "rows.{$index}.qty" => ['required', 'integer', 'min:1'],
                    ], $messages);
                }

                if ($row['price'] !== '') {
                    $this->validate([
                        "rows.{$index}.price" => ['required', 'numeric', 'min:0.01'],
                    ], $messages);
                }
            }

            $this->validateDuplicateCombinations();

            return;
        }

        $this->validate($rules, $messages);
        $this->validateDuplicateCombinations();

        if ($this->getErrorBag()->isNotEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages($this->getErrorBag()->toArray());
        }
    }

    private function validateDuplicateCombinations(): void
    {
        $combinations = [];

        foreach ($this->rows as $index => $row) {
            if (empty($row['item_id']) || empty($row['brand_id'])) {
                continue;
            }

            $key = $row['item_id'].'-'.$row['brand_id'];

            if (isset($combinations[$key])) {
                $this->addError("rows.{$index}.item_id", 'Duplicate item and brand combination.');
                $this->addError("rows.{$index}.brand_id", 'Duplicate item and brand combination.');
            }

            $combinations[$key] = true;
        }
    }

    public function render()
    {
        return view('livewire.purchase-form', [
            'items' => Item::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }
}
