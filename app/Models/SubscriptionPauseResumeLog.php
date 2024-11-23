<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPauseResumeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'order_id',
        'action',
        'pause_start_date',
        'pause_end_date',
        'resume_date',
        'new_end_date',
        'paused_days'
    ];

    // Relationship to Subscription model
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    // app/Models/SubscriptionPauseResumeLog.php

public function order()
{
    return $this->belongsTo(Order::class, 'order_id', 'order_id');
}

}
