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
        Schema::create('home_page_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discounted_product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('discount_percent', 10, 2);
            $table->string('discount_heading');
            $table->text('discount_subheading');
            $table->foreignId('featured_product_1_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('featured_product_2_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
