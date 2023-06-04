<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
                [
                    'service_name' => 'Wash Dry & Fold',
                    'service_price'=> 20,
                ],
                [
                    'service_name' => 'Ironing',
                    'service_price'=> 50,
                ]
            ];
            foreach ($services as $service){
                Service::updateOrCreate(
                    [
                       'service_name' => $service['service_name'], 
                    ],
                    [
                        'service_price' => $service['service_price'],
                    ]
                );
            };
           
    }
}
