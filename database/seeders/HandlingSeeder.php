<?php

namespace Database\Seeders;

use App\Models\Handling;
use Illuminate\Database\Seeder;

class HandlingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handlings = [
            [
                'handling_name' => 'Pickup & Delivery',
                'handling_price' => 40,
            ],
            [
                'handling_name' => 'Pickup',
                'handling_price' => 20,
            ],
            [
                'handling_name' => 'Delivery',
                'handling_price' => 20,
            ],
            [
                'handling_name' => 'Walkin',
                'handling_price' => 0,
            ]
        ];

        foreach ($handlings as $handling) {
            Handling::updateOrCreate([
                'handling_name' => $handling['handling_name'],
            ], [
                'handling_price' => $handling['handling_price'],
            ]);
        };
        
       
    }
}
