<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    use HasFactory;

    protected $fillable = ['locality_name', 'pincode','unique_code','status'];

    public function apartment()
    {
        return $this->hasMany(Apartment::class, 'locality_id', 'unique_code');
    }
}
