<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_date',
        'category',
        'direction',
        'source_type',
        'source_id',
        'amount',
        'mode_of_payment',
        'paid_by',
        'received_by',
        'description',
        'status',
    ];

    // Quick helpers
    public function scopeActive($q)
    {
        return $q->when($this->isFillable('status'), fn($qq) => $qq->where('status', 'active'), fn($qq) => $qq);
    }

    public function getSignedAmountAttribute(): float
    {
        return $this->direction === 'out' ? -1 * (float)$this->amount : (float)$this->amount;
    }
}
