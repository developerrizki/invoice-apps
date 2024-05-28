<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'uuid'];
    protected $hidden = ['id'];
    protected $fillable = [
        'uuid',
        'invoice_id',
        'item_name',
        'qty',
        'price'
    ];

    /**
     * Get the invoice that owns the InvoiceItem
     *
     * @return BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
