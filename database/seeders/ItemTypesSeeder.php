<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subcategories = [
            [
                'name' => 'bedsheet',
                'category_id' => 1
            ],
            [
                'name' => 'towel',
                'category_id' => 1
            ],
            [
                'name' => 'curtain',
                'category_id' => 1
            ],
            [
                'name' => 'pillowcase',
                'category_id' => 1
            ],
            [
                'name' => 'blanket',
                'category_id' => 1
            ],
            [
                'name' => 'tshirt',
                'category_id' => 2
            ],
            [
                'name' => 'shorts',
                'category_id' => 2
            ],
            [
                'name' => 'trousers',
                'category_id' => 2
            ],
            [
                'name' => 'jacket',
                'category_id' => 2
            ],
            [
                'name' => 'underwear',
                'category_id' => 2
            ],
            [
                'name' => 'blouse',
                'category_id' => 2
            ],
            [
                'name' => 'socks',
                'category_id' => 2
            ],
            [
                'name' => 'handkerchief',
                'category_id' => 2
            ],
            [
                'name' => 'pants',
                'category_id' => 2
            ],
            [
                'name' => 'bedsheet',
                'category_id' => 3
            ],
            [
                'name' => 'towel',
                'category_id' => 3
            ],
            [
                'name' => 'curtain',
                'category_id' => 3
            ],
            [
                'name' => 'pillowcase',
                'category_id' => 3
            ],
            [
                'name' => 'blanket',
                'category_id' => 3
            ],
            [
                'name' => 'tshirt',
                'category_id' => 4
            ],
            [
                'name' => 'shorts',
                'category_id' => 4
            ],
            [
                'name' => 'trousers',
                'category_id' => 4
            ],
            [
                'name' => 'jacket',
                'category_id' => 4
            ],
            [
                'name' => 'underwear',
                'category_id' => 4
            ],
            [
                'name' => 'blouse',
                'category_id' => 4
            ],
            [
                'name' => 'socks',
                'category_id' => 4
            ],
            [
                'name' => 'handkerchief',
                'category_id' => 4
            ],
            [
                'name' => 'pants',
                'category_id' => 4
            ],
        ];

        foreach ($subcategories  as $subcategory) {
            $newSubCategories = ItemType::updateOrCreate([
                'name' => $subcategory['name'],
                'category_id' => $subcategory['category_id'],
            ]);
        }
    }
}
