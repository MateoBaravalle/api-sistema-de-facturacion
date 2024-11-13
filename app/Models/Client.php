<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
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
        'credit_limit',
        'balance',
        'status',
        'created_at',
        'updated_at'
    ];
    
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'reference');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
