<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(AdminProfileSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ItemCategorySeeder::class);
        $this->call(ItemTypesSeeder::class);
        $this->call(HandlingSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(SettingsSeeder::class);
    }
}
