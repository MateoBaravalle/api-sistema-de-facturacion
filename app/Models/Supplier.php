<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'created_at',
        'updated_at'
    ];

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    public function orders()
    {
        return $this->morphMany(Order::class, 'reference');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
