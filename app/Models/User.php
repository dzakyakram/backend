<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama', 'email', 'password', 'role', 'avatar', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // Roles: admin | pengelola | pengguna
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isPengelola(): bool  { return in_array($this->role, ['admin', 'pengelola']); }

    public function locations()
    {
        return $this->hasMany(Location::class, 'user_id');
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Location::class, 'bookmarks', 'user_id', 'location_id')
                    ->withTimestamps();
    }

    // ── JWT ──────────────────────────────────────────────────
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return ['role' => $this->role];
    }
}
