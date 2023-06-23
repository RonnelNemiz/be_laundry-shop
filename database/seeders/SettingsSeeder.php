<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'name' => 'SMS',
                'value' => 'false'
            ],
        ];

        foreach ($settings  as $setting) {
            $newSettings = Setting::updateOrCreate([
                'name' => $setting['name'],
                'value' => $setting['value'],
            ]);
        }
    }
}
