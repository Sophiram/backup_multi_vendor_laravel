<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Review::create([
                'product_id'        => 1,
                'user_id'           => 1,
                'rating'            => 5,
                'review'            => 'Product is great!',
                'verified_purchase' => 1, // បន្ថែមតម្លៃនេះទៅតាម Table របស់អ្នក
            ]);
        }
}
