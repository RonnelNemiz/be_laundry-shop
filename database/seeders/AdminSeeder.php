<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            'email' => 'treslabanderas@admin.com',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'role' => 'Administrator',
            'password' => Hash::make('stress_labanderas'),
        ];

        User::updateOrCreate($params);
    }
}
