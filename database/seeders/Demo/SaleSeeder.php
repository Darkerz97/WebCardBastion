<?php

namespace Database\Seeders\Demo;

use App\Models\Customer;
use App\Models\Device;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $cashierRole = Role::query()->where('code', User::ROLE_CASHIER)->first();

        $cashier = User::query()->firstOrCreate(
            ['email' => 'cashier@cardbastion.test'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Caja Principal',
                'phone' => '5551112233',
                'password' => 'password',
                'role_id' => $cashierRole?->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (Sale::query()->exists()) {
            return;
        }

        $customer = Customer::query()->first();
        $device = Device::query()->first();
        $products = Product::query()->take(2)->get();

        if ($products->count() < 2) {
            return;
        }

        $sale = Sale::query()->create([
            'uuid' => (string) Str::uuid(),
            'customer_id' => $customer?->id,
            'user_id' => $cashier->id,
            'device_id' => $device?->id,
            'sale_number' => 'SALE-DEMO-0001',
            'subtotal' => $products->sum('price'),
            'discount' => 0,
            'total' => $products->sum('price'),
            'status' => Sale::STATUS_COMPLETED,
            'payment_status' => Sale::PAYMENT_STATUS_PAID,
            'sold_at' => now(),
        ]);

        foreach ($products as $product) {
            $sale->items()->create([
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
                'line_total' => $product->price,
            ]);
        }

        $sale->payments()->create([
            'method' => Payment::METHOD_CASH,
            'amount' => $sale->total,
            'reference' => 'DEMO',
            'notes' => 'Pago de demostración',
            'paid_at' => now(),
        ]);
    }
}
