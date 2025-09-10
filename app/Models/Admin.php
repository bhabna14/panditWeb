<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // ✅ correct import
use App\Models\MenuItem;                                   // ✅ ensure model import

class Admin extends Authenticatable implements AuthenticatableContract
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Auto-hash on set (avoids double-hashing)
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) =>
                !empty($value) && Hash::needsRehash($value) ? Hash::make($value) : $value
        );
    }

    /** Menus visible to this admin */
    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'admin_menu_item', 'admin_id', 'menu_item_id')
                    ->withTimestamps();
    }
}
