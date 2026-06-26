<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id')
            ->where('invoice_type', InvoiceType::Sales->value);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id')
            ->where('invoice_type', InvoiceType::Sales->value);
    }

    public function paidAmount(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    // Paid wins first; otherwise unpaid invoices become overdue after the due date.
    public function refreshPaymentStatus(): void
    {
        $paidAmount = $this->paidAmount();
        $status = InvoiceStatus::Pending->value;

        if ($paidAmount >= (float) $this->total && (float) $this->total > 0) {
            $status = InvoiceStatus::Paid->value;
        } elseif ($this->due_date && $this->due_date->lt(Carbon::today())) {
            $status = InvoiceStatus::Overdue->value;
        }

        $this->update(['status' => $status]);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeBetweenInvoiceDates(Builder $query, ?string $fromDate, ?string $toDate): Builder
    {
        return $query
            ->when($fromDate, fn (Builder $query) => $query->whereDate('invoice_date', '>=', $fromDate))
            ->when($toDate, fn (Builder $query) => $query->whereDate('invoice_date', '<=', $toDate));
    }

    public static function totalAmount(): float
    {
        return (float) self::sum('total');
    }

    public static function countByStatus(string $status): int
    {
        return self::status($status)->count();
    }

    public static function recentList(int $limit = 5)
    {
        return self::with('customer')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
