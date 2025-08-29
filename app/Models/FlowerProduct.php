<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerProduct extends Model
{
    use HasFactory;

    protected $table = 'flower_products';

    protected $fillable = ['product_id','name','odia_name','product_image', 'price','mrp','per_day_price','discount', 'description', 'category','mala_provided', 'is_flower_available','available_from','available_to','stock', 'duration','benefits','status'];

       // If you ever want to show a unified "item name" for flowers
    protected $appends = ['item_name'];

    public function getItemNameAttribute()
    {
        // For Flower category, "item_name" should read from product name.
        return $this->attributes['name'] ?? null;
    }
    public function pickupDetails()
    {
        return $this->hasMany(FlowerPickupDetails::class, 'product_id','flower_id');
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class, 'product_id', 'product_id');
    }

    public function pooja()
    {
        return $this->belongsTo(Poojalist::class, 'pooja_id', 'id');
    }

}