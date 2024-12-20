<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerPickupItems extends Model
{
    use HasFactory;
    protected $table = 'flower__pickup_items';
    protected $fillable = [
        'pick_up_id',
        'flower_id',
        'unit_id',
        'quantity',
        'price', // Nullable, updated later
    ];
    public function flower()
    {
        return $this->belongsTo(FlowerProduct::class, 'flower_id', 'product_id');
    }
    

    public function unit() {
        return $this->belongsTo(PoojaUnit::class,'unit_id');
    }


}
