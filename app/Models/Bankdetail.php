<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bankdetail extends Model
{
    use HasFactory;
    protected $table = 'bankdetails';
    protected $fillable = ['pandit_id','bankname','branchname','ifsccode','accname','accnumber','upi_number']; 

 
}
