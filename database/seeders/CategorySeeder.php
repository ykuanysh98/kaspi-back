<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Негізгі каталогтар
        $phones = Category::create(['name' => 'Телефоны']);
        $laptops = Category::create(['name' => 'Ноутбуки']);
        $tablets = Category::create(['name' => 'Планшеты']);

        // Субкаталогтар
        $smartphones = Category::create(['name' => 'Смартфоны', 'parent_id' => $phones->id]);
        $featurephones = Category::create(['name' => 'Кнопочные', 'parent_id' => $phones->id]);

        $gamingLaptops = Category::create(['name' => 'Геймерские', 'parent_id' => $laptops->id]);
        $ultrabooks = Category::create(['name' => 'Ультрабуки', 'parent_id' => $laptops->id]);

        $androidTablets = Category::create(['name' => 'Android', 'parent_id' => $tablets->id]);
        $ipad = Category::create(['name' => 'iPad', 'parent_id' => $tablets->id]);

        // Суб-субкаталогтар
        Category::create(['name' => 'Flagship', 'parent_id' => $smartphones->id]);
        Category::create(['name' => 'Budget', 'parent_id' => $smartphones->id]);
    }
}
