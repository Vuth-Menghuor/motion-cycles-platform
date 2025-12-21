<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relation to orders (1:1 per our design)
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            // Core payment fields (mirrors current fields kept on orders)
            $table->string('payment_method');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('qr_code_string')->nullable();
            $table->string('qr_md5_hash')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('payment_data')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            $table->timestamps();

            // Helpful indexes
            $table->index('payment_status');
            $table->index('created_at');
            $table->unique('transaction_id'); // allows multiple NULLs in MySQL, unique when present
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
