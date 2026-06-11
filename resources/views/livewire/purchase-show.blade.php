<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Purchase #{{ $purchase->id }}</h1>
            <p class="text-sm text-slate-600">Created {{ $purchase->created_at->format('M d, Y H:i') }}</p>
        </div>

        <div class="flex gap-2">
            @can('purchase_edit')
                <a
                    href="{{ route('purchases.edit', $purchase) }}"
                    wire:navigate
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Edit
                </a>
            @endcan

            <a
                href="{{ route('purchases.index') }}"
                wire:navigate
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900"
            >
                Back to List
            </a>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Item</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Brand</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Qty</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Price</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Line Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($purchase->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->item->name }}</td>
                        <td class="px-4 py-3">{{ $item->brand->name }}</td>
                        <td class="px-4 py-3">{{ $item->qty }}</td>
                        <td class="px-4 py-3">{{ number_format($item->price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format($item->lineTotal(), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50">
                <tr>
                    <td colspan="4" class="px-4 py-3 text-right font-semibold text-slate-700">Grand Total</td>
                    <td class="px-4 py-3 text-right text-lg font-semibold text-slate-800">
                        {{ number_format($purchase->total, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
