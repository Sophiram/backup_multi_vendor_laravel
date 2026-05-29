<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
    Schema::create('discounts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('code')->unique();
        $table->string('type');
        $table->decimal('value', 8, 2);
        $table->decimal('min_requirement', 8, 2)->nullable();
        $table->dateTime('start_date');
        $table->dateTime('end_date')->nullable();
        $table->boolean('status')->default(true);
        $table->integer('usage_limit_total')->nullable();
        $table->boolean('limit_per_user')->default(false);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
