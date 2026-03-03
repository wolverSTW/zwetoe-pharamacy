<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_STAFF = 'staff';
    const ROLE_CUSTOMER = 'customer';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approve',
        'phone',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approve' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_STAFF]) 
               && $this->is_approve === true;

    }

    public function isAdmin(): bool { return $this->role === self::ROLE_ADMIN; }
    public function isStaff(): bool { return $this->role === self::ROLE_STAFF; }
}