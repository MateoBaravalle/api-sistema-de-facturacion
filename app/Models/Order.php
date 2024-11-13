<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo; // Agregar esta línea

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
        'created_at',
        'updated_at'
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->using(OrderProduct::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
