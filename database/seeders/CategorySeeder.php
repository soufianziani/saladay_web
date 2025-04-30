<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Burgers', 'available' => true],
            ['name' => 'Pizza', 'available' => true],
            ['name' => 'Drinks', 'available' => true],
            ['name' => 'Sides', 'available' => true],
            ['name' => 'Desserts', 'available' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}