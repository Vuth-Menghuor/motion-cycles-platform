<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    // Fields that can be mass assigned
    protected $fillable = [
        'order_number',         // Unique order number
        'invoice_number',       // Unique invoice number
        'user_id',              // ID of the user who placed the order
        'customer_name',        // Customer's full name
        'customer_phone',       // Customer's phone number
        'customer_email',       // Customer's email address
        'shipping_address',     // Shipping address as array
        'items',                // Order items as array
        'subtotal',             // Subtotal before discounts/shipping
        'discount_amount',      // Discount amount applied
        'shipping_amount',      // Shipping cost
        'total_amount',         // Final total amount
        'currency',             // Currency code
        'promo_code',           // Applied promo code
        'order_status',         // Current order status
        'confirmed_at',         // When order was confirmed
        'processing_at',        // When order started processing
        'shipped_at',           // When order was shipped
        'delivered_at',         // When order was delivered
        'tracking_number',      // Shipping tracking number
        'notes',                // Additional notes
    ];

    // Type casting for attributes
    protected $casts = [
        'items' => 'array',                // Cast to array
        'shipping_address' => 'array',     // Cast to array
        'subtotal' => 'decimal:2',         // Decimal with 2 places
        'discount_amount' => 'decimal:2',  // Decimal with 2 places
        'shipping_amount' => 'decimal:2',  // Decimal with 2 places
        'total_amount' => 'decimal:2',     // Decimal with 2 places
        'confirmed_at' => 'datetime',      // Cast to datetime
        'processing_at' => 'datetime',     // Cast to datetime
        'shipped_at' => 'datetime',        // Cast to datetime
        'delivered_at' => 'datetime',      // Cast to datetime
    ];

    // Generate a unique order number
    public static function generateOrderNumber(): string
    {
        // Step 1: Loop until we find a unique order number
        do {
            // Step 2: Create order number with format ORD-YYYYMMDD-XXXX
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        // Step 3: Check if this order number already exists
        } while (self::where('order_number', $orderNumber)->exists());

        // Step 4: Return the unique order number
        return $orderNumber;
    }

    // Generate a unique invoice number
    public static function generateInvoiceNumber(): string
    {
        // Step 1: Loop until we find a unique invoice number
        do {
            // Step 2: Create invoice number with format INV-YYYYMMDD-XXXX
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        // Step 3: Check if this invoice number already exists
        } while (self::where('invoice_number', $invoiceNumber)->exists());

        // Step 4: Return the unique invoice number
        return $invoiceNumber;
    }

    // Order belongs to a User (each order belongs to one user)
    public function user(): BelongsTo
    {
        // Step 1: Return belongsTo relationship to User model
        return $this->belongsTo(User::class);
    }

    // Order has one Payment (each order can have one payment record)
    public function payment()
    {
        // Step 1: Return hasOne relationship to Payment model
        return $this->hasOne(Payment::class);
    }

    // Accessor for payment_method - gets from payment relationship
    public function getPaymentMethodAttribute()
    {
        return $this->payment?->payment_method;
    }

    // Accessor for payment_status - gets from payment relationship
    public function getPaymentStatusAttribute()
    {
        return $this->payment?->payment_status ?? 'pending';
    }

    // Accessor for qr_code_string - gets from payment relationship
    public function getQrCodeStringAttribute()
    {
        return $this->payment?->qr_code_string;
    }

    // Accessor for qr_md5_hash - gets from payment relationship
    public function getQrMd5HashAttribute()
    {
        return $this->payment?->qr_md5_hash;
    }

    // Accessor for payment_completed_at - gets from payment relationship
    public function getPaymentCompletedAtAttribute()
    {
        return $this->payment?->payment_completed_at;
    }

    // Check if payment is completed
    public function isPaymentCompleted(): bool
    {
        // Step 1: Check if payment relationship exists and is completed
        return $this->payment?->isCompleted() ?? false;
    }

    // Mark order as payment completed
    public function markAsPaymentCompleted(array $transactionData = []): void
    {
        // Step 1: Update payment record with completion details
        if ($this->payment) {
            $this->payment->markAsCompleted($transactionData);
        }

        // Step 2: Update order status
        $this->update([
            'order_status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Step 3: Log transaction data if provided
        if (!empty($transactionData)) {
            Log::info('Payment transaction data', [
                'order_id' => $this->id,
                'order_number' => $this->order_number,
                'transaction_data' => $transactionData,
            ]);
        }
    }
}