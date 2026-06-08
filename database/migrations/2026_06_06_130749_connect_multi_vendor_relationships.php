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
        // ១. កែប្រែតារាង stores (ដក user_id ចេញ រួចថែម vendor_id ភ្ជាប់ទៅ vendors)
        if (Schema::hasTable('stores')) {
            Schema::table('stores', function (Blueprint $table) {
                // ដក Foreign Key និង Column user_id ចេញ (បើមាន)
                if (Schema::hasColumn('stores', 'user_id')) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (\Exception $e) {
                        // រំលងបើមិនមាន Foreign Key ភ្ជាប់ពីមុន
                    }
                    $table->dropColumn('user_id');
                }

                // បន្ថែម Column vendor_id ទៅក្នុងតារាង stores
                if (!Schema::hasColumn('stores', 'vendor_id')) {
                    $table->foreignId('vendor_id')
                          ->after('id')
                          ->nullable() // ដាក់ nullable ដើម្បីកុំឱ្យគាំងទិន្នន័យចាស់
                          ->constrained('vendors')
                          ->onDelete('cascade');
                }
            });
        }

        // ២. កែប្រែតារាង order_items (បង្កើត Foreign Key ឱ្យ vendor_id ស្គាល់ vendors)
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                // ដោយសារ columns មានរួចហើយ យើងគ្រាន់តែបង្កើតលក្ខខណ្ឌ Foreign Key ប៉ុណ្ណោះ
                try {
                    $table->foreign('vendor_id')
                          ->references('id')
                          ->on('vendors')
                          ->onDelete('cascade');
                } catch (\Exception $e) {
                    // រំលងបើវាមាន Foreign Key រួចហើយ
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                try {
                    $table->dropForeign(['vendor_id']);
                } catch (\Exception $e) {}
            });
        }

        if (Schema::hasTable('stores')) {
            Schema::table('stores', function (Blueprint $table) {
                try {
                    $table->dropForeign(['vendor_id']);
                    $table->dropColumn('vendor_id');
                } catch (\Exception $e) {}

                if (!Schema::hasColumn('stores', 'user_id')) {
                    $table->foreignId('user_id')->nullable();
                }
            });
        }
    }
};
