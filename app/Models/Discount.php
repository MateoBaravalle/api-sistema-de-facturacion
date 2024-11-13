<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percentage',
        'fixed_amount',
        'discount_type',
        'supplier_id',
        'category',
        'product_id',
        'client_id',
        'start_date',
        'end_date',
        'min_purchase',
        'is_active',
        'is_accumulative',
        'created_at',
        'updated_at'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
