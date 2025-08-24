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
        Schema::create('type_infos', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Multilingual name (ar, en)
            $table->json('description')->nullable(); // Multilingual description (ar, en)
            $table->enum('type', ['terms', 'whoWeAre', 'condition']);
            $table->timestamps();
            
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_infos');
    }
};
