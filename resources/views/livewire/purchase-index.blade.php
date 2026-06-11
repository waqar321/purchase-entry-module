<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">Purchases</h1>
            <p class="text-sm text-slate-600">View and manage purchase records.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            @can('purchase_create')
                <a
                    href="{{ route('purchases.create') }}"
                    wire:navigate
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    New Purchase
                </a>
            @endcan

            @can('legacy_migrate')
                <button
                    type="button"
                    wire:click="runLegacyMigration"
                    wire:confirm="Run legacy data migration? This is idempotent and safe to run multiple times."
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Run Legacy Migration
                </button>
            @endcan
        </div>
    </div>

    @if ($migrationMessage)
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            {{ $migrationMessage }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">ID</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Items</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Total</th>
                    <th class="px-4 py-3 text-left font-medium text-slate-600">Created</th>
                    <th class="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($purchases as $purchase)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">#{{ $purchase->id }}</td>
                        <td class="px-4 py-3">{{ $purchase->items->count() }} item(s)</td>
                        <td class="px-4 py-3 font-medium">{{ number_format($purchase->total, 2) }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $purchase->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a
                                    href="{{ route('purchases.show', $purchase) }}"
                                    wire:navigate
                                    class="text-blue-600 hover:text-blue-700"
                                >
                                    View
                                </a>

                                @can('purchase_edit')
                                    <a
                                        href="{{ route('purchases.edit', $purchase) }}"
                                        wire:navigate
                                        class="text-slate-600 hover:text-slate-800"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('purchase_delete')
                                    <button
                                        type="button"
                                        wire:click="delete({{ $purchase->id }})"
                                        wire:confirm="Delete this purchase?"
                                        class="text-red-600 hover:text-red-700"
                                    >
                                        Delete
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                            No purchases found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $purchases->links() }}
    </div>
</div>
