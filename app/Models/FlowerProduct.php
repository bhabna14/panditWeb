<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerProduct extends Model
{
    use HasFactory;
    protected $table = 'flower_products';
    protected $fillable = ['product_id','name','product_image', 'price','mrp', 'description', 'category', 'stock', 'duration','status'];


    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class, 'product_id','flower_id');
    }
//     public function pickupDetails()
// {
//     return $this->hasMany(FlowerPickupDetails::class, 'flower_id', 'id');
// }

}
