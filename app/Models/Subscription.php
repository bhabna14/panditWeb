<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions'; // Ensure this matches your actual table name

    // Add all fields you want to allow for mass assignment
    protected $fillable = [
        'subscription_id',
        'order_id',
        'user_id',
        'product_id',
        'start_date',
        'end_date',
        'is_active',
        'pause_start_date',
        'pause_end_date',
        'status'
    ];
    // In Subscription.php model
public static function expireIfEnded()
{
    $today = Carbon::today();

    // Find active subscriptions where the end date has passed
    $subscriptions = self::where('is_active', true)
        ->where('end_date', '<', $today)
        ->get();

    foreach ($subscriptions as $subscription) {
        $subscription->status = 'expired';
        $subscription->is_active = false;
        $subscription->save();

        Log::info('Subscription expired', [
            'order_id' => $subscription->order_id,
            'user_id' => $subscription->user_id,
        ]);
    }
}
public function pauseResumeLogs()
{
    return $this->hasMany(SubscriptionPauseResumeLog::class);
}

public function relatedOrder()
{
    return $this->belongsTo(Order::class, 'order_id'); // Adjust 'order_id' as per your actual foreign key.
}

}
