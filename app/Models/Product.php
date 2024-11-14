<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'supplier_id',
        'name',
        'purchase_price',
        'sale_price',
        'category',
        'stock',
        'status'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'string',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->using(OrderProduct::class)
            ->withPivot(['quantity', 'price', 'discount']);
    }

    public function price_history()
    {
        return $this->hasMany(PriceHistory::class);
    }
}
