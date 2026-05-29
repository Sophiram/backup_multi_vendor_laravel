<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 0, // admin
            'email_verified_at' => now()
        ]);

        // Create vendor users
        $vendor1 = User::create([
            'name' => 'Vendor One',
            'email' => 'vendor1@example.com',
            'password' => Hash::make('password'),
            'role' => 1, // vendor
            'email_verified_at' => now()
        ]);

        $vendor2 = User::create([
            'name' => 'Vendor Two',
            'email' => 'vendor2@example.com',
            'password' => Hash::make('password'),
            'role' => 1, // vendor
            'email_verified_at' => now()
        ]);

        // Create regular users
        $user1 = User::create([
            'name' => 'John Customer',
            'email' => 'customer1@example.com',
            'password' => Hash::make('password'),
            'role' => 2, // user
            'email_verified_at' => now()
        ]);

        $user2 = User::create([
            'name' => 'Jane Customer',
            'email' => 'customer2@example.com',
            'password' => Hash::make('password'),
            'role' => 2, // user
            'email_verified_at' => now()
        ]);

        // Create categories
        $electronics = Category::create(['category_name' => 'Electronics']);
        $clothing = Category::create(['category_name' => 'Clothing']);
        $books = Category::create(['category_name' => 'Books']);

        // Create subcategories
        $phones = SubCategory::create(['category_id' => $electronics->id, 'subcategory_name' => 'Phones']);
        $laptops = SubCategory::create(['category_id' => $electronics->id, 'subcategory_name' => 'Laptops']);
        $menshirts = SubCategory::create(['category_id' => $clothing->id, 'subcategory_name' => "Men's Shirts"]);
        $womenshirts = SubCategory::create(['category_id' => $clothing->id, 'subcategory_name' => "Women's Shirts"]);

        // Create stores for vendors
        $store1 = Store::create([
            'store_name' => 'Tech Paradise',
            'slug' => 'tech-paradise',
            'details' => 'Your one-stop shop for all electronics needs',
            'user_id' => $vendor1->id
        ]);

        $store2 = Store::create([
            'store_name' => 'Fashion Hub',
            'slug' => 'fashion-hub',
            'details' => 'Trendy fashion items for everyone',
            'user_id' => $vendor2->id
        ]);

        // Create products for vendor 1
        $product1 = Product::create([
            'product_name' => 'iPhone 15 Pro',
            'description' => 'Latest Apple iPhone with advanced features',
            'sku' => 'IPHONE15PRO001',
            'vendor_id' => $vendor1->id,
            'category_id' => $electronics->id,
            'subcategory_id' => $phones->id,
            'store_id' => $store1->id,
            'regular_price' => 999.99,
            'discounted_price' => 899.99,
            'tax_rate' => 8.5,
            'stock_quantity' => 50,
            'stock_status' => 'instock',
            'slug' => 'iphone-15-pro',
            'visibility' => true,
            'meta_title' => 'iPhone 15 Pro',
            'meta_description' => 'Buy latest iPhone 15 Pro from Tech Paradise',
            'status' => 'Published'
        ]);

        $product2 = Product::create([
            'product_name' => 'Dell XPS 13 Laptop',
            'description' => 'Ultra-portable 13-inch laptop with powerful performance',
            'sku' => 'DELLXPS13001',
            'vendor_id' => $vendor1->id,
            'category_id' => $electronics->id,
            'subcategory_id' => $laptops->id,
            'store_id' => $store1->id,
            'regular_price' => 1299.99,
            'discounted_price' => 1199.99,
            'tax_rate' => 8.5,
            'stock_quantity' => 30,
            'stock_status' => 'instock',
            'slug' => 'dell-xps-13',
            'visibility' => true,
            'meta_title' => 'Dell XPS 13 Laptop',
            'meta_description' => 'Buy Dell XPS 13 from Tech Paradise',
            'status' => 'Published'
        ]);

        // Create products for vendor 2
        $product3 = Product::create([
            'product_name' => 'Premium Mens Shirt',
            'description' => 'High-quality cotton mens shirt, perfect for casual and formal wear',
            'sku' => 'MENSHIRT001',
            'vendor_id' => $vendor2->id,
            'category_id' => $clothing->id,
            'subcategory_id' => $menshirts->id,
            'store_id' => $store2->id,
            'regular_price' => 49.99,
            'discounted_price' => 39.99,
            'tax_rate' => 5.0,
            'stock_quantity' => 100,
            'stock_status' => 'instock',
            'slug' => 'premium-mens-shirt',
            'visibility' => true,
            'meta_title' => 'Premium Mens Shirt',
            'meta_description' => 'Buy premium quality mens shirts from Fashion Hub',
            'status' => 'Published'
        ]);

        $product4 = Product::create([
            'product_name' => 'Womens Summer Dress',
            'description' => 'Comfortable and stylish summer dress for women',
            'sku' => 'WOMENDRESS001',
            'vendor_id' => $vendor2->id,
            'category_id' => $clothing->id,
            'subcategory_id' => $womenshirts->id,
            'store_id' => $store2->id,
            'regular_price' => 59.99,
            'discounted_price' => 44.99,
            'tax_rate' => 5.0,
            'stock_quantity' => 80,
            'stock_status' => 'instock',
            'slug' => 'womens-summer-dress',
            'visibility' => true,
            'meta_title' => 'Womens Summer Dress',
            'meta_description' => 'Buy beautiful womens summer dresses from Fashion Hub',
            'status' => 'Published'
        ]);

        // Add product images (using placeholder URLs for now)
        ProductImage::create([
            'product_id' => $product1->id,
            'image_path' => 'product_images/iphone15pro.jpg',
            'is_primary' => true
        ]);

        ProductImage::create([
            'product_id' => $product2->id,
            'image_path' => 'product_images/dellxps13.jpg',
            'is_primary' => true
        ]);

        ProductImage::create([
            'product_id' => $product3->id,
            'image_path' => 'product_images/mensshirt.jpg',
            'is_primary' => true
        ]);

        ProductImage::create([
            'product_id' => $product4->id,
            'image_path' => 'product_images/womensdress.jpg',
            'is_primary' => true
        ]);

        echo "Demo data seeded successfully!\n";
        echo "Admin Login: admin@example.com / password\n";
        echo "Vendor 1 Login: vendor1@example.com / password\n";
        echo "Vendor 2 Login: vendor2@example.com / password\n";
        echo "Customer 1 Login: customer1@example.com / password\n";
        echo "Customer 2 Login: customer2@example.com / password\n";
    }
}
