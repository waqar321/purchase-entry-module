<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    protected $fillable = ['total'];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function recalculateTotal(): void
    {
        $this->update([
            'total' => $this->items()->sum(DB::raw('qty * price')),
        ]);
    }
}
