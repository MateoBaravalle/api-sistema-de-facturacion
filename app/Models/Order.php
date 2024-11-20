<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'reference_type',
        'order_date',
        'status',
        'discount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'discount' => 'float',
        'total_amount' => 'decimal:2',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderProduct::class)
            ->withPivot(['quantity', 'price', 'discount']);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
