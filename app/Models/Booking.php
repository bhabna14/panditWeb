<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';

    protected $fillable = [
        'booking_id',
        'user_id',
        'pandit_id',
        'pooja_id',
        'address_id',
        'pooja_fee',
        'advance_fee',
        'booking_date',
        'booking_end_time',
        'status',
        'payment_status',
        'application_status',
        'pooja_status',
        'canceled_at',
        'cancel_reason',
        'refund_method',
        'refund_amount',
    ];

    public function poojaList()
    {
        return $this->belongsTo(Poojalist::class, 'pooja_id','id');
    }

    public function poojaStatus()
    {
        return $this->hasOne(Poojastatus::class, 'booking_id', 'booking_id')->whereColumn('pooja_id', 'pooja_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->booking_id = 'BKD-' . strtoupper(uniqid());
        });
    }

    public function pandit()
    {
        return $this->belongsTo(Profile::class, 'pandit_id', 'id');
    }

   

    public function pooja()
    {
        return $this->belongsTo(Poojadetails::class, 'pooja_id', 'pooja_id');
    }
    
    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'userid');
    }
    public function ratings()
    {
        return $this->belongsTo(Rating::class, 'booking_id' ,'booking_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'booking_id');
    }
}
