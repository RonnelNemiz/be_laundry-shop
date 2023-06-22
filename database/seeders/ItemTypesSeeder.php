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
                'parent_category' => 1
            ],
            [
                'name' => 'towel',
                'parent_category' => 1
            ],
            [
                'name' => 'curtain',
                'parent_category' => 1
            ],
            [
                'name' => 'pillowcase',
                'parent_category' => 1
            ],
            [
                'name' => 'blanket',
                'parent_category' => 1
            ],
            [
                'name' => 'tshirt',
                'parent_category' => 2
            ],
            [
                'name' => 'shorts',
                'parent_category' => 2
            ],
            [
                'name' => 'trousers',
                'parent_category' => 2
            ],
            [
                'name' => 'jacket',
                'parent_category' => 2
            ],
            [
                'name' => 'underwear',
                'parent_category' => 2
            ],
            [
                'name' => 'blouse',
                'parent_category' => 2
            ],
            [
                'name' => 'socks',
                'parent_category' => 2
            ],
            [
                'name' => 'handkerchief',
                'parent_category' => 2
            ],
            [
                'name' => 'pants',
                'parent_category' => 2
            ],
            [
                'name' => 'bedsheet',
                'parent_category' => 3
            ],
            [
                'name' => 'towel',
                'parent_category' => 3
            ],
            [
                'name' => 'curtain',
                'parent_category' => 3
            ],
            [
                'name' => 'pillowcase',
                'parent_category' => 3
            ],
            [
                'name' => 'blanket',
                'parent_category' => 3
            ],
            [
                'name' => 'tshirt',
                'parent_category' => 4
            ],
            [
                'name' => 'shorts',
                'parent_category' => 4
            ],
            [
                'name' => 'trousers',
                'parent_category' => 4
            ],
            [
                'name' => 'jacket',
                'parent_category' => 4
            ],
            [
                'name' => 'underwear',
                'parent_category' => 4
            ],
            [
                'name' => 'blouse',
                'parent_category' => 4
            ],
            [
                'name' => 'socks',
                'parent_category' => 4
            ],
            [
                'name' => 'handkerchief',
                'parent_category' => 4
            ],
            [
                'name' => 'pants',
                'parent_category' => 4
            ],
        ];

        foreach ($subcategories  as $subcategory) {
            $newSubCategories = ItemType::updateOrCreate([
                'name' => $subcategory['name'],
                'parent_category' => $subcategory['parent_category'],
            ]);
        }
    }
}
