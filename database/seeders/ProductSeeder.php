<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Тазалау
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = Category::pluck('id')->toArray();

        for ($i = 1; $i <= 100; $i++) {
            Product::create([
                'name' => 'Product ' . $i,
                'description' => Str::random(100),
                'price' => rand(5000, 500000),
                'category_id' => $categories[array_rand($categories)],
            ]);
        }
    }
}

