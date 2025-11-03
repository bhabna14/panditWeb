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
        // 'status', // only if you actually have this column
    ];

    // If your DB column is DATE only, change to 'date'
    protected $casts = [
        'date'   => 'datetime:Y-m-d H:i:s',
        'amount' => 'decimal:2',
    ];

    /** Use like: OfficeFund::query()->active() */
    public function scopeActive($query)
    {
        $model = $query->getModel();
        return $model->isFillable('status')
            ? $query->where('status', 'active')
            : $query;
    }
}
