<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Product;
use App\Models\ProductImage; // адаптируй егер модель басқа жерде болса

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        // Сен берген суреттер (осыларды қойып шығады)
        $defaultImages = [
            'https://placehold.co/600x400?text=Product',
            'https://placehold.co/600x400?text=Product',
            'https://placehold.co/600x400?text=Product',
        ];

        // 1) табл. бағанының атауын анықтау (кең таралған атауларды тексереміз)
        $table = 'product_images';
        $candidates = ['path', 'image', 'image_url', 'url', 'src'];

        $col = null;
        foreach ($candidates as $candidate) {
            if (Schema::hasColumn($table, $candidate)) {
                $col = $candidate;
                break;
            }
        }

        // Егер колонка табылмаса — қауіпсіз түрде 'path' колонкасын қосамыз
        if (!$col) {
            // Егер кесте болмаған жағдайда миграциялық емес өзгеріс тәуекелі бар,
            // бірақ сидерді жеңілдету үшін колонка қосамыз.
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn('product_images', 'path')) {
                    $table->string('path')->nullable();
                }
            });

            $col = 'path';
            $this->command->info("Колонка табылмады — 'path' колонкасын қостым.");
        } else {
            $this->command->info("Қолданылатын колонка: {$col}");
        }

        // 2) Бар суреттерді тазалау (опциялы) — қайта толтырамыз
        DB::table($table)->delete();
        $this->command->info("Барлық жазбалар өшірілді: {$table}");

        // 3) Әр өнімге сурет қосу
        $products = Product::all();
        $this->command->info("Products found: " . $products->count());

        foreach ($products as $product) {
            // таңдау: әр өнімге 1 сурет қою (рандом таңдау)
            $url = $defaultImages[array_rand($defaultImages)];

            // Егер кестеде timestamps қажет болса, қосып жіберем
            $payload = [
                'product_id' => $product->id,
                $col => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table($table)->insert($payload);
        }

        $this->command->info("Барлық өнімдерге суреттер қойылды.");
    }
}
