<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poojastatus extends Model
{
    use HasFactory;
    protected $table = "pooja_status";
    
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
