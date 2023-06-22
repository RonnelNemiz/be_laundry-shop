<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class AdminProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newCategories = Profile::updateOrCreate([
            'user_id' => 1,
            'first_name' => 'Stress',
            'last_name' => 'Labanderas',
            'purok' => 'Ambot',
            'brgy' => 'Di Makita',
            'municipality' => 'Hanap-hanap',
            'contact_number' => '0912-345-6789',
            'land_mark' => 'Biringan',
        ]);
    }
}
