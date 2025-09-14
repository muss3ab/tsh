<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Category::factory(10)->create();
        Product::factory(50)->create();
        Order::factory(20)->create();
        OrderItem::factory(100)->create();
    }
}
