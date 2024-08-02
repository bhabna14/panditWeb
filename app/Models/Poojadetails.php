<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poojadetails extends Model
{
    use HasFactory;
    protected $table = "pandit_poojadetails";
    protected $fillable = ['pandit_id', 'pooja_id', 'pooja_name', 'pooja_photo', 'pooja_fee', 'pooja_video', 'pooja_duration', 'pooja_done'];

    public function poojalist()
    {
        return $this->belongsTo(Poojalist::class, 'pooja_id','id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'pandit_id', 'pandit_id')->where('pandit_status', 'accepted');
    }
    public function bookings()
{
    return $this->hasMany(Booking::class, 'pooja_id', 'pooja_id');
}

   
}

