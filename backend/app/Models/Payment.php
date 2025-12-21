<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    // Fields that can be mass assigned
    protected $fillable = [
        'order_id',             // ID of the associated order
        'payment_method',       // Payment method used
        'payment_status',       // Current payment status
        'qr_code_string',       // QR code for payment
        'qr_md5_hash',          // MD5 hash of QR code
        'payment_gateway_string', // Payment gateway transaction reference
        'payment_data',         // Additional payment data as array
        'payment_completed_at', // When payment was completed
        'amount',               // Payment amount
        'currency',             // Currency code
    ];

    // Type casting for attributes
    protected $casts = [
        'payment_data' => 'array',           // Cast to array
        'payment_completed_at' => 'datetime', // Cast to datetime
        'amount' => 'decimal:2',             // Decimal with 2 places
    ];

    // Payment belongs to an Order (each payment belongs to one order)
    public function order(): BelongsTo
    {
        // Step 1: Return belongsTo relationship to Order model
        return $this->belongsTo(Order::class);
    }

    // Check if payment is completed
    public function isCompleted(): bool
    {
        // Step 1: Check if payment status equals 'completed'
        return $this->payment_status === 'completed';
    }

    // Mark payment as completed
    public function markAsCompleted(array $paymentData = []): void
    {
        // Step 1: Update payment with completion status and timestamp
        $this->update([
            'payment_status' => 'completed',
            'payment_completed_at' => now(),
            'payment_data' => $paymentData,
        ]);
    }

    // Scope: Get pending payments
    public function scopePending($query)
    {
        // Step 1: Filter query where payment status is 'pending'
        return $query->where('payment_status', 'pending');
    }

    // Scope: Get completed payments
    public function scopeCompleted($query)
    {
        // Step 1: Filter query where payment status is 'completed'
        return $query->where('payment_status', 'completed');
    }
}