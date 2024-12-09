<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
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
        'payment_terms',
        'balance',
        'status',
    ];

    protected $casts = [
        'payment_terms' => 'integer',
        'balance' => 'decimal:2',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'reference');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'related');
    }
}
