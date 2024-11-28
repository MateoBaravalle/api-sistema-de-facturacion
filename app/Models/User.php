<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'lastname',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    // Implement the required methods from JWTSubject
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // Mutators/Accessors
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->lastname}";
    }

    // Helper methods
    public function hasAnyRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return $this->roles->whereIn('name', $roles)->count() > 0;
        }
        return $this->hasRole($roles);
    }

    public function hasAllRoles(string|array $roles): bool
    {
        if (is_array($roles)) {
            return $this->roles->whereIn('name', $roles)->count() === count($roles);
        }
        return $this->hasRole($roles);
    }
}
