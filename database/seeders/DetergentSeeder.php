<?php

namespace Database\Seeders;

use App\Models\Detergent;
use Illuminate\Database\Seeder;

class DetergentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $detergents = [
            [
                'detergent_name' => 'Surf',
                'detergent_price' => 290,
                'detergent_scoop' => 40,
            ],
            [
                'detergent_name' => 'Ariel',
                'detergent_price' => 300,
                'detergent_scoop' => 40,
            ],
            [
                'detergent_name' => 'Breeze',
                'detergent_price' => 290,
                'detergent_scoop' => 40,
            ]
        ];
        foreach ($detergents as $detergent){
            Detergent::updateOrCreate(
                [
                    'detergent_name' => $detergent['detergent_name']
                ],
                [
                    'detergent_price' => $detergent['detergent_price'],
                    'detergent_scoop' => $detergent['detergent_scoop'],
                ]
            );
        };
            
    }
}
