<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

// class Superadmin extends Model
class SuperAdmin extends Authenticatable implements AuthenticatableContract
{
    use HasFactory;
    protected $table = 'superadmins';
}
