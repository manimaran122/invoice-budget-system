<?php

namespace App\Models;

use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_type',
        'invoice_id',
        'product_service_id',
        'description',
        'quantity',
        'price',
        'tax',
        'discount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function productService(): BelongsTo
    {
        return $this->belongsTo(ProductService::class);
    }

    public function scopePurchase($query)
    {
        return $query->where('invoice_type', InvoiceType::Purchase->value);
    }

    public function scopeSales($query)
    {
        return $query->where('invoice_type', InvoiceType::Sales->value);
    }
}
