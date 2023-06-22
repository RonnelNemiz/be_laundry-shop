<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
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
                'service_id' => 1
            ],
            [
                'name' => 'White Beddings & Bathroom Accessories',
                'price' => 250,
                'service_id' => 1
            ],
            [
                'name' => 'Colored Garments',
                'price' => 150,
                'service_id' => 1
            ],
            [
                'name' => 'White Garments',
                'price' => 300,
                'service_id' => 1
            ],
        ];

        foreach ($categories as $category) {
            $newCategories = ItemCategory::updateOrCreate([
                'name' => $category['name'],
                'price' => $category['price'],
                'service_id' => $category['service_id']
            ]);
        }
    }
}
