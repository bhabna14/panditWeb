<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;
    protected $fillable = [
        'variant_id', 'product_id', 'title', 'price'
    ];

    public function product()
    {
        return $this->belongsTo(Poojaitemlists::class, 'product_id');
    }
}
