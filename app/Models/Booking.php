<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'pandit_id',
        'pooja_id',
        'address_id',
        'pooja_fee',
        'advance_fee',
        'booking_date',
        'booking_time',
        'status',
        'application_status'
    ];

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
        return $this->belongsTo(Poojadetails::class, 'pooja_id','id');
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'userid');
    }
}
