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
                'name' => 'Colored Bedsheet Towel',
                'price' => 200,
                'children' => [
                    'colorbdst_bedsheet',
                    'colorbdst_towel',
                    'colorbdst_curtain',
                    'colorbdst_pillowcase',
                    'colorbdst_blanket'
                ],
            ],
            [
                'name' => 'Colored Garment Towel',
                'price' => 150,
                'children' => [
                    'colorgart_tshirt',
                    'colorgart_shorts',
                    'colorgart_trousers',
                    'colorgart_jacket',
                    'colorgart_underwear',
                    'colorgart_blouse',
                    'colorgart_socks',
                    'colorgart_handkerchief',
                    'colorgart_pants'
                ],
            ],
            [
                'name' => 'White Bedsheet Towel',
                'price' => 250,
                'children' => [
                    'whitebdst_bedsheet',
                    'whitebdst_towel',
                    'whitebdst_curtain',
                    'whitebdst_pillowcase',
                    'whitebdst_blanket'
                ]
            ],
            [
                'name' => 'White Garment Towel',
                'price' => 300,
                'children' => [
                    'whitegart_tshirt',
                    'whitegart_shorts',
                    'whitegart_trousers',
                    'whitegart_jacket',
                    'whitegart_underwear',
                    'whitegart_blouse',
                    'whitegart_socks',
                    'whitegart_handkerchief',
                    'whitegart_pants'
                ],
            ],
        ];

        foreach ($categories  as $category) {
            $newCategories = Category::updateOrCreate([
                'name' => $category['name'],
                'price' => $category['price'],
            ]);

            foreach ($category['children'] as $child) {
                Category::updateOrCreate([
                    'name' => $child,
                    'parent_id' => $newCategories->id,
                ]);
            }
        }
    }
}
