<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $table = 'flower__apartment';

    protected $fillable = ['locality_id','apartment_name'];
    
    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id', 'id');
    }
    
    
}
