<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;

    protected $table = 'product__package_item';

    protected $fillable = [
        'product_id','item_name','quantity','unit','price'
    ];

//     public function item()
// {
//     return $this->belongsTo(Poojaitemlists::class, 'item_id');
// }

// public function variant()
// {
//     return $this->belongsTo(Variant::class, 'variant_id');
// }

}
