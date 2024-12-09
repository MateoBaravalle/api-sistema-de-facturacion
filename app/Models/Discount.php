<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'related_id',
        'related_type',
        'name',
        'discount_type',
        'discount_value',
        'category',
        'start_date',
        'end_date',
        'min_purchase',
        'is_active',
        'is_accumulative',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'is_active' => 'boolean',
        'is_accumulative' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeAccumulative($query): Builder
    {
        return $query->where('is_accumulative', true);
    }

    public function scopeNonAccumulative($query): Builder
    {
        return $query->where('is_accumulative', false);
    }

    public function scopeSuppliers($query): Builder
    {
        return $query->where('related_type', Supplier::class);
    }

    public function scopeClients($query): Builder
    {
        return $query->where('related_type', Client::class);
    }

    public function scopeCategory($query, $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeProducts($query): Builder
    {
        return $query->where('related_type', Product::class);
    }
}
