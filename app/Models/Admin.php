<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // ✅ needed for Str::startsWith
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MenuItem;

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

    /** Menus visible to this admin */
    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'admin_menu_item', 'admin_id', 'menu_item_id')
                    ->withTimestamps();
    }

    /**
     * Hash password on set (avoids double-hashing if it already looks hashed).
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (empty($value)) {
                    return $value;
                }
                $looksHashed = is_string($value) && (
                    Str::startsWith($value, '$2y$') ||     // bcrypt
                    Str::startsWith($value, '$argon2i$') ||
                    Str::startsWith($value, '$argon2id$')
                );
                return $looksHashed ? $value : Hash::make($value);
            }
        );
    }
}
