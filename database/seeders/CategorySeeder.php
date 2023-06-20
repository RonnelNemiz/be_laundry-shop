<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Colored Beddings & Bathroom Accessories',
                'price' => 200,
            ],
            [
                'name' => 'White Beddings & Bathroom Accessories',
                'price' => 250,
            ],
            [
                'name' => 'Colored Garments',
                'price' => 150,
            ],
            [
                'name' => 'White Garments',
                'price' => 300,
            ],
        ];

        foreach ($categories as $category) {
            $newCategories = Category::updateOrCreate([
                'name' => $category['name'],
                'price' => $category['price'],
            ]);
        }
    }
}
