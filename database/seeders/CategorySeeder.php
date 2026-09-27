<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Engine Parts',
            'Brakes',
            'Electrical',
            'Tires',
            'Lubricants',
            'Transmission',
            'Accessories',
            'Maintenance Parts',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate([
                'name' => $name
            ]);
        }
    }
}