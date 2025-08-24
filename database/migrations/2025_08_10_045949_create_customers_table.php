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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('address')->nullable(); // Multilingual address (ar, en)
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('coins')->default(0); // Coin balance for customers
            $table->timestamps();
            
            $table->index(['user_id', 'city_id']);
            $table->index('coins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
