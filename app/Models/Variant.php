<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id', 'title', 'price'
    ];

    public function product()
    {
        return $this->belongsTo(Poojaitemlists::class, 'item_id');
    }
}
