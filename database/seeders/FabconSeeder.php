<?php

namespace Database\Seeders;

use App\Models\Fabcon;
use Illuminate\Database\Seeder;

class FabconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fabcons = [
            [
                'fabcon_name' => 'Downy',
                'fabcon_price' => 300,
                'fabcon_scoop' => 30,
            ],
            [
                'fabcon_name' => 'Del',
                'fabcon_price' => 280,
                'fabcon_scoop' => 30,
            ],   
        ];
        foreach ($fabcons as $fabcon){
            Fabcon::updateOrCreate(
                [
                    'fabcon_name' => $fabcon['fabcon_name']
                ],
                [
                    'fabcon_price' => $fabcon['fabcon_price'],
                    'fabcon_scoop' => $fabcon['fabcon_scoop'],
                ]
            );
        };
    }
}
