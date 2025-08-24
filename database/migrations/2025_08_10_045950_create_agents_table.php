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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('name'); // Multilingual name (ar, en)
            $table->string('license_number')->nullable();
            $table->string('phone');
            $table->json('address'); // Multilingual address (ar, en)
            $table->string('logo')->nullable();
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('online');
            $table->boolean('is_active')->default(true);
            $table->json('working_hours')->nullable();
            $table->json('service_areas')->nullable(); // Array of city IDs they serve
            $table->json('specializations')->nullable(); // Array of service types
            $table->timestamps();
            
            $table->index(['city_id', 'status']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
