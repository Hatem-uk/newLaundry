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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade'); // Seller: laundry or agent
            $table->json('name'); // Multilingual name (ar, en)
            $table->json('description'); // Multilingual description (ar, en)
            $table->integer('coin_cost')->nullable(); // Used when customers pay by coins
            $table->decimal('price', 12, 2)->nullable(); // Used when laundries buy from agents (cash-like)
            $table->integer('quantity')->default(1);
            $table->string('type'); // e.g., washing, ironing, agent_supply
            $table->enum('status', ['pending', 'active', 'inactive', 'approved', 'rejected'])->default('pending');
            $table->string('image')->nullable();
            $table->timestamps();
            
            $table->index(['provider_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};


