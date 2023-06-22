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
                'name' => 'Wash, Dry & Fold',
                'description' => 'Convenient and professional laundry cleaning, drying, and folding for customers. Simply drop off your clothes and receive them back clean, dry, and neatly folded.'
            ],
            [
                'name' => 'Dry Cleaning',
                'description' => 'specialized cleaning for delicate or non-water washable garments. It involves using solvents instead of water to remove stains and dirt from fabrics. This professional service ensures your clothes are meticulously cleaned, preserving their quality and extending their lifespan.'
            ],
        ];

        foreach ($services  as $service) {
            $newService = Service::updateOrCreate([
                'name' => $service['name'],
                'description' => $service['description'],
            ]);
        }
    }
}
