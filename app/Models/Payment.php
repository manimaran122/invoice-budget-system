<?php

namespace App\Models;

use App\Enums\InvoiceType;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_type',
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function scopePurchase($query)
    {
        return $query->where('invoice_type', InvoiceType::Purchase->value);
    }

    public function scopeSales($query)
    {
        return $query->where('invoice_type', InvoiceType::Sales->value);
    }
}
