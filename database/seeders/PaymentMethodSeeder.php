<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = [
            [
                'name' => 'GCash',
                'logo' => '',
                'recipient' => '',
                'number' => '0912-345-6789',
                'payment_method_id' => 1,
                'special_instructions' => 'Delectus deleniti autem vel dolorum sed?',
            ],
            [
                'name' => 'Cash',
                'logo' => '',
                'recipient' => '',
                'number' => '0912-987-6543',
                'payment_method_id' => 2,
                'special_instructions' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit.',
            ],
        ];

        foreach ($paymentMethods  as $paymentMethod) {
            $newPaymentMethods = PaymentMethod::updateOrCreate([
                'name' => $paymentMethod['name'],
                'logo' => $paymentMethod['logo'],
                'recipient' => $paymentMethod['recipient'],
                'number' => $paymentMethod['number'],
                'special_instructions' => $paymentMethod['special_instructions'],
            ]);
        }
    }
}
