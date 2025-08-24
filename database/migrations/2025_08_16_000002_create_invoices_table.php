<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Recipient snapshot
            $table->foreignId('payer_id')->constrained('users')->onDelete('cascade'); // Who paid snapshot
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade'); // Seller snapshot
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'online', 'coins']);
            $table->enum('status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->json('meta')->nullable(); // Free-form data
            $table->timestamp('paid_at')->nullable(); // When payment was completed
            $table->timestamp('refunded_at')->nullable(); // When refund was processed
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['order_id']);
            $table->index(['user_id', 'status']);
            $table->index(['payer_id', 'status']);
            $table->index(['provider_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
