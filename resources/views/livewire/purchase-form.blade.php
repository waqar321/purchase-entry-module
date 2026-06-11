{{-- @entangle('rows') It synchronizes Alpine's rows with Livewire's $rows. --}}
{{-- 3. Computed Property: total  from get total() --}}

<div
    x-data="{
        rows: @entangle('rows'),
        get total() {
            return this.rows.reduce((sum, row) => {
                const qty = parseFloat(row.qty) || 0;
                const price = parseFloat(row.price) || 0;
                return sum + (qty * price);
            }, 0);
        },
        formatCurrency(value) {
            return new Intl.NumberFormat(
                    'en-US', 
                    { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    }
                ).format(value);
        }
    }"
>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-800">
            {{ $purchase ? 'Edit Purchase' : 'New Purchase' }}
        </h1>
        <p class="text-sm text-slate-600">Add items with quantity and price. Total updates automatically.</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                <div class="grid grid-cols-12 gap-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <div class="col-span-3">Item</div>
                    <div class="col-span-3">Brand</div>
                    <div class="col-span-2">Qty</div>
                    <div class="col-span-2">Price</div>
                    <div class="col-span-2 text-right">Line Total</div>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach ($rows as $index => $row)
                    <div class="grid grid-cols-12 items-start gap-3 px-4 py-4" wire:key="row-{{ $index }}">
                        <div class="col-span-3">
                            <select
                                wire:model.live.debounce.500ms="rows.{{ $index }}.item_id"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">Select item</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error("rows.{$index}.item_id")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-3">
                            <select
                                wire:model.live.debounce.500ms="rows.{{ $index }}.brand_id"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                                <option value="">Select brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error("rows.{$index}.brand_id")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <input
                                type="number"
                                min="1"
                                wire:model.live.debounce.500ms="rows.{{ $index }}.qty"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="Qty"
                            >
                            @error("rows.{$index}.qty")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <input
                                type="number"
                                min="0.01"
                                step="0.01"
                                wire:model.live.debounce.500ms="rows.{{ $index }}.price"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                placeholder="Price"
                            >
                            @error("rows.{$index}.price")
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-span-2 flex items-center justify-end gap-2 pt-2">
                            <span
                                class="text-sm font-medium text-slate-700"
                                x-text="formatCurrency((parseFloat(rows[{{ $index }}]?.qty) || 0) * (parseFloat(rows[{{ $index }}]?.price) || 0))"
                            ></span>

                            @if (count($rows) > 1)
                                <button
                                    type="button"
                                    wire:click="removeRow({{ $index }})"
                                    class="text-red-600 hover:text-red-700"
                                    title="Remove row"
                                >
                                    &times;
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <button
                type="button"
                wire:click="addRow"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                + Add Row
            </button>

            <div class="rounded-lg border border-slate-200 bg-white px-6 py-3 text-right">
                <span class="text-sm text-slate-600">Grand Total:</span>
                <span class="ml-2 text-xl font-semibold text-slate-800" x-text="formatCurrency(total)"></span>
            </div>
        </div>

        <div class="flex gap-3">
            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                {{ $purchase ? 'Update Purchase' : 'Save Purchase' }}
            </button>

            <a
                href="{{ route('purchases.index') }}"
                wire:navigate
                class="rounded-lg border border-slate-300 bg-white px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
            >
                Cancel
            </a>
        </div>
    </form>
</div>
