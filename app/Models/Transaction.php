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
        'due_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
