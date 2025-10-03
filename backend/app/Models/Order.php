<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'invoice_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'items',
        'subtotal',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'currency',
        'promo_code',
        'payment_method',
        'payment_status',
        'qr_code_string',
        'qr_md5_hash',
        'transaction_id',
        'payment_data',
        'payment_completed_at',
        'order_status',
        'notes',
    ];

    protected $casts = [
        'items' => 'array',
        'payment_data' => 'array',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_completed_at' => 'datetime',
    ];

    protected $dates = [
        'payment_completed_at',
    ];

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payment is completed
     */
    public function isPaymentCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Mark payment as completed
     */
    public function markAsPaymentCompleted(array $paymentData = []): void
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_completed_at' => now(),
            'payment_data' => $paymentData,
            'order_status' => 'confirmed', // Move to confirmed status after payment
        ]);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute(): string
    {
        $symbol = $this->currency === 'KHR' ? '៛' : '$';
        return $symbol . number_format($this->total_amount, 2);
    }

    /**
     * Scope for pending payments
     */
    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompletedPayment($query)
    {
        return $query->where('payment_status', 'completed');
    }
}