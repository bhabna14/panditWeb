<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $table = 'orders';

    protected $fillable = [
        'request_id',
        'product_id',
        'user_id',
        'quantity',
        'total_price',
        'requested_flower_price',
        'delivery_charge',
        'rider_id',
        'referral_id',
        'order_id',  // Add this line
        'address_id',
        'suggestion',
        'created_at'
    ];
    public function delivery()
    {
        return $this->belongsTo(DeliveryHistory::class, 'order_id', 'order_id');
    }
    public function rider()
    {
        return $this->belongsTo(RiderDetails::class, 'rider_id', 'rider_id');
    }
    public function flowerRequest()
    {
        return $this->belongsTo(FlowerRequest::class, 'request_id', 'request_id');
    }
    public function subscription()
{
    return $this->hasOne(Subscription::class, 'order_id', 'order_id');
}
public function flowerPayments()
{
    return $this->hasMany(FlowerPayment::class, 'order_id', 'order_id');
}
public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'userid');
}
public function flowerProduct()
{
    return $this->belongsTo(FlowerProduct::class, 'product_id', 'product_id');
}
public function address()
{
    return $this->belongsTo(UserAddress::class, 'address_id');
}
// app/Models/Order.php

public function pauseResumeLogs()
{
    return $this->hasMany(SubscriptionPauseResumeLog::class, 'order_id', 'order_id');
}
public function marketingFollowUps()
{
    return $this->hasMany(MarketingFollowUp::class, 'order_id', 'order_id');
}

public function deliveryCustomizeHistory()
{
    return $this->hasOne(DeliveryCustomizeHistory::class, 'rider_id', 'rider_id');
}

}
