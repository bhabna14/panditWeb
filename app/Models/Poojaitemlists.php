<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poojaitemlists extends Model
{
    use HasFactory;

    protected $table = "poojaitem_list";

    protected $fillable = [
        'id',
        'item_name',
        'slug',
        'product_type',
        'status',
    ];

    public function variants()
    {
        return $this->hasMany(Variant::class, 'item_id');
    }
}
