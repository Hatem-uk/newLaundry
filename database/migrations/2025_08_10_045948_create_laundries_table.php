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
       Schema::create('laundries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('name'); // Multilingual name (ar, en)
            $table->string('logo')->nullable();
            $table->json('address'); // Multilingual address (ar, en)
            $table->string('phone');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('online');
            $table->boolean('is_active')->default(true);
            $table->json('working_hours')->nullable();
            $table->boolean('delivery_available')->default(false);
            $table->boolean('pickup_available')->default(true);
            $table->decimal('average_rating', 3, 2)->default(0.00); // For customer ratings
            $table->integer('total_ratings')->default(0); // Total number of ratings
            $table->text('description')->nullable()->after('working_hours');
            $table->integer('delivery_radius')->default(10)->after('description');
            $table->string('website')->nullable()->after('delivery_radius');
            $table->string('facebook')->nullable()->after('website');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('whatsapp')->nullable()->after('instagram');
            $table->decimal('latitude', 10, 8)->nullable()->after('whatsapp');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->timestamps();
            
            $table->index(['city_id', 'status']);
            $table->index(['status', 'is_active']);
            $table->index('average_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundries');
    }
};
