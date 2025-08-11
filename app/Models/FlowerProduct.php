<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerProduct extends Model
{
    use HasFactory;
    protected $table = 'flower_products';
    
    protected $fillable = ['product_id','name','odia_name','product_image', 'price','mrp', 'description', 'category','mala_provided', 'flower_available','stock', 'duration','benefits','status'];

    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class, 'product_id','flower_id');
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class, 'product_id', 'product_id');
    }
    
}
