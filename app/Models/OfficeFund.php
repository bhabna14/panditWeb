<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeFund extends Model
{
    use HasFactory;

    protected $table = 'office_fund_received';

    protected $fillable = [
        'date',
        'categories',
        'amount',
        'mode_of_payment',
        'paid_by',
        'received_by',
        'description',
    ];

    protected $casts = [
        'date'   => 'date',      // change to 'datetime' if needed
        'amount' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        $model = $query->getModel();

        return $model->isFillable('status')
            ? $query->where('status', 'active')
            : $query;
    }
}
