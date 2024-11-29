<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoojaUnit extends Model
{
    use HasFactory;
    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class, 'unit_id');
    }
    
}
