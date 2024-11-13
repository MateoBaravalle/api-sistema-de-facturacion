<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
        'product_id',
        'old_price',
        'new_price',
        'change_date',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'change_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
