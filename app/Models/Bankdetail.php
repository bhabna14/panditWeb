<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bankdetail extends Model
{
    use HasFactory;
    protected $table = 'bankdetails';
    protected $fillable = ['userid', 'bankname','branchname','ifsccode','accname','accnumber']; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
