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
        // បន្ថែម column status (លំនាំដើមគឺ 'active')
        $table->string('status')->default('active')->after('id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('carts', function (Blueprint $table) {
        $table->dropColumn('status');
    });
    }
};
