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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Payer
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('cascade'); // Receiver (defaults to user_id)
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade'); // Seller: admin, laundry, or agent
            $table->unsignedBigInteger('target_id'); // Package ID or Service ID
            $table->enum('target_type', ['package', 'service']); // What is being purchased
             $table->integer('coins'); // Positive when granted, negative when consumed
            $table->decimal('price', 12, 2)->default(0); // Cash component if any
            $table->enum('status', ['pending', 'in_process', 'completed', 'canceled'])->default('pending');
            $table->json('meta')->nullable(); // Free-form data (e.g., quantity, notes)
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['recipient_id', 'status']);
            $table->index(['provider_id', 'status']);
            $table->index(['target_type', 'target_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
