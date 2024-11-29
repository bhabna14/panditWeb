<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderArea extends Model
{
    use HasFactory;
    
    protected $table = 'flower__order_assign';

    protected $fillable = [
        'rider_id',
        'locality_id',
        'apartment_id',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id', 'id');
    }
    
    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id', 'id');
    }

      public function rider()
      {
          return $this->belongsTo(RiderDetails::class, 'rider_id', 'rider_id');
      }
    
    
}
