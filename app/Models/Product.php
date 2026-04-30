<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use CrudTrait;

    protected $fillable = [
        'internal_code', 'barcode', 'name', 'short_name', 'description',
        'category_id', 'brand_id', 'preferred_supplier_id', 'sale_unit',
        'units_per_pack', 'cost_average', 'last_purchase_cost', 'tax_rate',
        'min_stock', 'track_batches', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'units_per_pack' => 'decimal:4',
            'cost_average' => 'decimal:4',
            'last_purchase_cost' => 'decimal:4',
            'tax_rate' => 'decimal:2',
            'min_stock' => 'decimal:4',
            'track_batches' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function preferredSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'preferred_supplier_id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function productBatches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function saleLines(): HasMany
    {
        return $this->hasMany(SaleLine::class);
    }

    public function purchaseLines(): HasMany
    {
        return $this->hasMany(PurchaseLine::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
