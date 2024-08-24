<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poojalist extends Model
{
    use HasFactory;
    protected $table = "pooja_list";
    protected $fillable = ['pooja_name','pooja_photo','short_description'];
    public function poojadetails()
    {
        return $this->hasMany(Poojadetails::class, 'pooja_id');
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'pooja_id', 'id');
    }
}
