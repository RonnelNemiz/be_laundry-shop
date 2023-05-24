<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payments = [
            [
                'payment_name' => 'GCash',
            ],
            [
                'payment_name' => 'COD',
            ],     
        ];
        foreach ($payments as $payment) {
            Payment::updateOrCreate([
                'payment_name' => $payment['payment_name'],
            ],
          );
        }; 
    }
}
