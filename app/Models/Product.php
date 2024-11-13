<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'purchase_price',
        'sale_price',
        'category',
        'stock',
        'status'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->using(OrderProduct::class);
    }

    public function price_history()
    {
        return $this->hasMany(PriceHistory::class);
    }
}
