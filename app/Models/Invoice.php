<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    public const STATUS_PAYMENT_PENDING = 'pending';
    public const STATUS_PAYMENT_PAID = 'paid';
    public const STATUS_PAYMENT_EXPIRED = 'expired';
    public const STATUS_PAYMENT_CANCELLED = 'cancelled';
    public const STATUS_PAYMENT_SETTLED = 'settled';

    protected $guarded = ['id', 'uuid'];
    protected $hidden = ['id'];
    protected $fillable = [
        'uuid',
        'user_id',
        'customer_id',
        'due_date',
        'number',
        'total',
        'checkout_link',
        'status_payment',
        'external_id'
    ];

    /**
     * Get all of the Item for the Invoice
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    /**
     * Get the user that create the Invoice
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the customer that owns the Invoice
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
