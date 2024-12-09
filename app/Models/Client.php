<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cuit',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'credit_limit',
        'balance',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'reference');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'related');
    }

    // Scopes
    public function scopeActive($query): Builder
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeInactive($query): Builder
    {
        return $query->whereNotNull('deleted_at');
    }

    public function scopePositive($query): Builder
    {
        return $query->where('balance', '>=', 0);
    }

    public function scopeNegative($query): Builder
    {
        return $query->where('balance', '<', 0);
    }

    public function scopeProvince($query, $province): Builder
    {
        return $query->where('province', $province);
    }

    public function scopeCity($query, $city): Builder
    {
        return $query->where('city', $city);
    }

    // Métodos
    public function addBalance($amount): void
    {
        $this->balance += $amount;
        $this->save();
    }

    public function subtractBalance($amount): void
    {
        $this->balance -= $amount;
        $this->save();
    }
}
