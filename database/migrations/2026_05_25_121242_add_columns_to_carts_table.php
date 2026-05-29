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
   Schema::table('carts', function (Blueprint $table) {
        // ប្រើ Schema::hasColumn ដើម្បីពិនិត្យថាតើវាមានរួចហើយឬនៅ
        if (!Schema::hasColumn('carts', 'items_count')) {
            $table->integer('items_count')->default(0);
        }

        if (!Schema::hasColumn('carts', 'total_amount')) {
            $table->decimal('total_amount', 10, 2)->default(0.00);
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            //
        });
    }
};
