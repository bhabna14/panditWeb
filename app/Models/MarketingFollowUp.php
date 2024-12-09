<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingFollowUp extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'subscription_id',
        'user_id',
        'followup_date',
        'note',
        'created_at',
        'updated_at',
    ];
}
