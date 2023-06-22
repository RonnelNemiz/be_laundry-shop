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
                'name' => 'Pickup & Delivery',
                'price' => 40,
            ],
            [
                'name' => 'Pickup',
                'price' => 20,
            ],
            [
                'name' => 'Delivery',
                'price' => 20,
            ],
            [
                'name' => 'Walkin',
                'price' => 0,
            ]
        ];

        foreach ($handlings as $handling) {
            Handling::updateOrCreate([
                'name' => $handling['name'],
            ], [
                'price' => $handling['price'],
            ]);
        };
    }
}
