<?php

namespace Database\Seeders\Demo;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Damian Ortega',
                'phone' => '5512345678',
                'email' => 'damian@example.com',
                'notes' => 'Cliente recurrente de torneos.',
                'credit_balance' => 0,
                'active' => true,
            ],
            [
                'name' => 'Andrea Castillo',
                'phone' => '5587654321',
                'email' => 'andrea@example.com',
                'notes' => 'Interesada en preventas.',
                'credit_balance' => 120,
                'active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::query()->updateOrCreate(
                ['email' => $customer['email']],
                ['uuid' => (string) Str::uuid(), ...$customer]
            );
        }
    }
}
