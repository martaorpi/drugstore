<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use CrudTrait;

    protected $fillable = [
        'sale_number', 'branch_id', 'warehouse_id', 'cash_register_id', 'cash_session_id',
        'user_id', 'customer_id', 'price_list_id', 'channel', 'status',
        'subtotal_ex_tax', 'tax_total', 'discount_total', 'rounding', 'grand_total',
        'invoice_type', 'invoice_number', 'electronic_authorization',
        'invoiced_at', 'completed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_ex_tax' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'rounding' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'invoiced_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Sale $sale) {
            if (blank($sale->sale_number)) {
                $sale->sale_number = 'V-'.now()->format('YmdHis').'-'.random_int(1000, 9999);
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function saleLines(): HasMany
    {
        return $this->hasMany(SaleLine::class);
    }

    public function salePayments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }
}
