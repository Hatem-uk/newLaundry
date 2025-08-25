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
        Schema::table('laundries', function (Blueprint $table) {
            $table->text('description')->nullable()->after('working_hours');
            $table->integer('delivery_radius')->default(10)->after('description');
            $table->string('website')->nullable()->after('delivery_radius');
            $table->string('facebook')->nullable()->after('website');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('whatsapp')->nullable()->after('instagram');
            $table->decimal('latitude', 10, 8)->nullable()->after('whatsapp');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'delivery_radius',
                'website',
                'facebook',
                'instagram',
                'whatsapp',
                'latitude',
                'longitude'
            ]);
        });
    }
};
