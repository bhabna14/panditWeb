<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $fillable = [
        'booking_id',
        'user_id',
        'payment_id',
        'payment_status',
        'paid',
        'payment_type',
        'payment_method',
        'cancel_reason',
        'refund_method',
        'refund_amount',
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
