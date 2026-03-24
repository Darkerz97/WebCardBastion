<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SaleService
{
    public function create(array $payload): Sale
    {
        return DB::transaction(function () use ($payload): Sale {
            $items = Arr::get($payload, 'items', []);
            $payments = Arr::get($payload, 'payments', []);
            $discount = (float) Arr::get($payload, 'discount', 0);
            $status = Arr::get($payload, 'status', Sale::STATUS_COMPLETED);
            $soldAt = Arr::get($payload, 'sold_at', now());

            if ($items === []) {
                throw new InvalidArgumentException('La venta debe contener al menos un producto.');
            }

            $sale = Sale::create([
                'uuid' => Arr::get($payload, 'uuid', (string) Str::uuid()),
                'customer_id' => Arr::get($payload, 'customer_id'),
                'user_id' => Arr::get($payload, 'user_id'),
                'device_id' => Arr::get($payload, 'device_id'),
                'sale_number' => Arr::get($payload, 'sale_number', $this->generateSaleNumber()),
                'order_channel' => Arr::get($payload, 'order_channel', Sale::CHANNEL_POS),
                'contact_name' => Arr::get($payload, 'contact_name'),
                'contact_email' => Arr::get($payload, 'contact_email'),
                'contact_phone' => Arr::get($payload, 'contact_phone'),
                'notes' => Arr::get($payload, 'notes'),
                'subtotal' => 0,
                'discount' => $discount,
                'total' => 0,
                'status' => $status,
                'payment_status' => Sale::PAYMENT_STATUS_UNPAID,
                'sold_at' => Carbon::parse($soldAt),
            ]);

            $subtotal = 0.0;

            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) ($item['unit_price'] ?? $product->price);
                $lineTotal = $quantity * $unitPrice;

                if ($status === Sale::STATUS_COMPLETED && $product->stock < $quantity) {
                    throw new InvalidArgumentException("Stock insuficiente para {$product->name}.");
                }

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                if ($status === Sale::STATUS_COMPLETED) {
                    $product->decrement('stock', $quantity);
                }

                $subtotal += $lineTotal;
            }

            $sale->update([
                'subtotal' => $subtotal,
                'total' => max($subtotal - $discount, 0),
            ]);

            foreach ($payments as $payment) {
                $this->registerPayment($sale, $payment);
            }

            return $sale->load(['customer', 'user.role', 'device', 'items.product', 'payments']);
        });
    }

    public function registerPayment(Sale $sale, array $payload): Payment
    {
        return DB::transaction(function () use ($sale, $payload): Payment {
            $sale->refresh();

            $amount = (float) $payload['amount'];
            $method = $payload['method'];
            $alreadyPaid = (float) $sale->payments()->sum('amount');
            $newTotal = $alreadyPaid + $amount;
            $saleTotal = (float) $sale->total;

            if ($method !== Payment::METHOD_CASH && $newTotal > $saleTotal) {
                throw new InvalidArgumentException('El total pagado no puede exceder el total de la venta.');
            }

            $payment = $sale->payments()->create([
                'method' => $method,
                'amount' => $amount,
                'reference' => $payload['reference'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'paid_at' => $payload['paid_at'] ?? now(),
            ]);

            $this->refreshPaymentStatus($sale);

            return $payment;
        });
    }

    public function refreshPaymentStatus(Sale $sale): Sale
    {
        $paidAmount = (float) $sale->payments()->sum('amount');
        $saleTotal = (float) $sale->total;

        $status = match (true) {
            $paidAmount <= 0 => Sale::PAYMENT_STATUS_UNPAID,
            $paidAmount < $saleTotal => Sale::PAYMENT_STATUS_PARTIAL,
            default => Sale::PAYMENT_STATUS_PAID,
        };

        $sale->update(['payment_status' => $status]);

        return $sale->refresh();
    }

    protected function generateSaleNumber(): string
    {
        $datePrefix = now()->format('Ymd');
        $lastSale = Sale::query()
            ->whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $nextSequence = $lastSale ? ((int) substr($lastSale->sale_number, -4)) + 1 : 1;

        return sprintf('SALE-%s-%04d', $datePrefix, $nextSequence);
    }
}
