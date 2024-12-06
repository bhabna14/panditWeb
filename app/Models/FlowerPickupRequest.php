<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerPickupRequest extends Model
{
    use HasFactory;
    protected $table = 'flower__pickup_request';

    protected $fillable = [
        'rider_id',
        'pickup_date',
        'pickdetails',
        'status',
    ];
    public function rider() {
        return $this->belongsTo(RiderDetails::class ,'rider_id', 'rider_id');
    }
}
