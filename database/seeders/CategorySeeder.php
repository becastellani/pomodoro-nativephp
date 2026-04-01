<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Work',     'color' => '#FF6B6B'],
            ['name' => 'Study',    'color' => '#4ECDC4'],
            ['name' => 'Personal', 'color' => '#45B7D1'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}