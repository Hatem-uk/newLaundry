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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('laundry_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating')->comment('التقييم من 1 إلى 5');
            $table->text('comment')->nullable()->comment('تعليق العميل');
            $table->string('service_type')->nullable()->comment('نوع الخدمة المقيمة');
            $table->timestamps();

            // فهارس لتحسين الأداء
            $table->index(['laundry_id', 'rating']);
            $table->index(['customer_id', 'laundry_id']);
            $table->index(['order_id']);
            
            // منع التقييم المكرر لنفس الطلب
            $table->unique(['customer_id', 'laundry_id', 'order_id'], 'unique_customer_laundry_order_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
