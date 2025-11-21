<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions'; // Ensure this matches your actual table name
    
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
        'new_date',
        'status'
    ];

    
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'userid');
    }

    public function flowerPayments()
    {
        return $this->hasMany(FlowerPayment::class, 'order_id', 'order_id');
    }

    public static function expireIfEnded()
    {
        $today = Carbon::today();
    
        // Find active subscriptions where the end date has passed
        $subscriptions = self::whereIn('status', ['active', 'paused'])
            ->where('end_date', '<', $today)
            ->get();
    
        foreach ($subscriptions as $subscription) {
            $subscription->status = 'expired';
            $subscription->save();
    
            Log::info('Subscription expired', [
                'order_id' => $subscription->order_id,
                'user_id' => $subscription->user_id,
            ]);
        }
    }

    public function pauseResumeLogs()
    {
        return $this->hasMany(SubscriptionPauseResumeLog::class, 'subscription_id', 'subscription_id');
    }

    public function relatedOrder()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');  // Adjust 'order_id' if necessary
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');  // Adjust 'order_id' if necessary
    }

    public function flowerProducts()
    {
        return $this->belongsTo(FlowerProduct::class, 'product_id', 'product_id');
    }

    public function pauseResumeLog()
    {
        return $this->hasMany(SubscriptionPauseResumeLog::class, 'order_id', 'order_id');
    }


    // public function scopeActiveOn($q, Carbon $date)
    // {
    //     return $q->where(function ($q) {
    //             $q->where('status', 'active')->orWhere('is_active', 1);
    //         })
    //         ->whereDate('start_date', '<=', $date->toDateString())
    //         ->whereDate('end_date', '>=', $date->toDateString())
    //         ->where(function ($q) use ($date) {
    //             $q->whereNull('pause_start_date')
    //             ->orWhereNull('pause_end_date')
    //             ->orWhereDate('pause_end_date', '<', $date->toDateString())
    //             ->orWhereDate('pause_start_date', '>', $date->toDateString());
    //         });
    // }

    public function latestPayment()
    {
        return $this->hasOne(FlowerPayment::class, 'order_id', 'order_id')->latestOfMany();
    }

    public function latestPaidPayment()
    {
        return $this->hasOne(FlowerPayment::class, 'order_id', 'order_id')
                    ->where('payment_status', 'paid')
                    ->latestOfMany();
    }

    public function scopeActiveOn($query, $date)
    {
        $d = Carbon::parse($date)->toDateString();

        return $query
            ->whereNotIn('status', ['expired', 'dead'])
            ->where(function ($q) {
                $q->whereIn('status', ['active', 'pending', 'paused'])
                  ->orWhere('is_active', 1);
            })
            ->whereDate('start_date', '<=', $d)
            ->where(function ($q) use ($d) {
                // If new_date is present we compare with that; else with end_date; allow open-ended too.
                $q->whereDate(DB::raw('COALESCE(new_date, end_date, start_date)'), '>=', $d);
            })
            // Exclude rows where the target date falls inside the pause window (inclusive)
            ->where(function ($q) use ($d) {
                $q->whereNull('pause_start_date')
                  ->orWhereNull('pause_end_date')
                  ->orWhereDate('pause_start_date', '>', $d)
                  ->orWhereDate('pause_end_date', '<', $d);
            });
    }

    public function scopeWithoutOtherActiveOrPending(Builder $query): Builder
    {
        return $query->whereDoesntExist(function ($sub) {
            $sub->select(DB::raw(1))
                ->from('subscriptions as s2')
                ->whereColumn('s2.user_id', 'subscriptions.user_id') // same user
                ->whereColumn('s2.id', '!=', 'subscriptions.id')      // different record
                ->whereIn('s2.status', ['active', 'pending']);        // other sub is active/pending
        });
    }
}
