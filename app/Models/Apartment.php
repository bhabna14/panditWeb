<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $table = 'flower__apartment';

    protected $fillable = ['locality_id','apartment_name','status'];

    // Apartment.locality_id => Locality.unique_code
    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id', 'unique_code');
    }
}
