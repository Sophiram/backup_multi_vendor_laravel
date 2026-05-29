<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    \App\Models\Cart::create([
        'user_id' => 1,
        'items_count' => 3,
        'total_amount' => 145.00,
        'status' => 'converted'
    ]);
}
}
