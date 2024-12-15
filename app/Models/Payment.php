<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'payment_method',
        'amount',
        'status',
        'payment_date',
        'proof_file_url',
        'verified_at',
        'external_transaction_id',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'proof_file_url' => 'string',
        'amount' => 'decimal:2',
    ];

    // Relación con la transacción original
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Scopes para facilitar consultas
    public function scopePending($query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query): Builder
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeUnverified($query): Builder
    {
        return $query->whereNull('verified_at');
    }

    // Métodos de utilidad para verificación
    public function markAsVerified(): void
    {
        $this->update([
            'verified_at' => now(),
        ]);
    }

    // Generar un número de referencia único para cada pago
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->reference_number = strtoupper(uniqid('PAY-'));
        });
    }
}
