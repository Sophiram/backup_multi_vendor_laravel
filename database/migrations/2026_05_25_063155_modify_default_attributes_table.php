<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('default_attributes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->foreignId('attribute_id')->constrained();
        $table->unsignedBigInteger('attribute_value_id')->nullable();
        $table->decimal('additional_price', 8, 2)->default(0.00);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::table('default_attributes', function (Blueprint $table) {
        $table->string('attribute_value');
        $table->dropForeign(['product_id']);
        $table->dropForeign(['attribute_id']);
        $table->dropColumn(['product_id', 'attribute_id', 'attribute_value_id', 'additional_price']);
    });
}
};
