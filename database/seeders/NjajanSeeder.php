<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NjajanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kategori Cafe
        $coffee = Category::create(['name' => 'Coffee']);
        $nonCoffee = Category::create(['name' => 'Non-Coffee']);
        $snack = Category::create(['name' => 'Snack']);

        // 2. Buat Menu Coffee Shop
        Menu::create([
            'category_id' => $coffee->id,
            'name' => 'Es Kopi Susu Gula Aren',
            'price' => 18000,
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => $nonCoffee->id,
            'name' => 'Matcha Latte',
            'price' => 22000,
            'is_available' => true
        ]);

        Menu::create([
            'category_id' => $snack->id,
            'name' => 'Croissant Almond',
            'price' => 25000,
            'is_available' => true
        ]);

        // 3. Buat Meja (Misal Meja 1 sampai 3)
        // Hash ini yang nanti jadi isi QR Code
        Table::create(['number' => '01', 'hash' => Str::random(10)]);
        Table::create(['number' => '02', 'hash' => Str::random(10)]);
        Table::create(['number' => '03', 'hash' => Str::random(10)]);
    }
}