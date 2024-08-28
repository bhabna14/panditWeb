<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanditDevice extends Model
{
    use HasFactory;
    protected $table = 'pandit_devices';

    protected $fillable = [
        'pandit_id', 'device_id', 'platform','device_model'
    ];

    // Define the relationship back to PanditLogin
 
    public function pandit()
    {
        return $this->belongsTo(PanditLogin::class, 'pandit_id', 'pandit_id');
    }
}
