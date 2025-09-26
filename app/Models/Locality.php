<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    use HasFactory;

    protected $fillable = ['locality_name', 'pincode','unique_code','status'];

    /**
     * One locality has many apartments.
     * foreign key on Apartment: locality_id
     * local key here: unique_code
     */
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'locality_id', 'unique_code');
    }
}
