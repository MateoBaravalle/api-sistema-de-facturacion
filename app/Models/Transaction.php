<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'reference_type',
        'description',
        'amount',
        'status',
        'transaction_date',
        'due_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'transaction_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
