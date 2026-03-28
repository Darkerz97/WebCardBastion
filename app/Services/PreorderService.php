<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Preorder;
use App\Models\PreorderPayment;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PreorderService
{
    public function create(array $payload): Preorder
    {
        return DB::transaction(function () use ($payload): Preorder {
            $items = Arr::get($payload, 'items', []);
            $payments = Arr::get($payload, 'payments', []);
            $discount = (float) Arr::get($payload, 'discount', 0);

            if ($items === []) {
                throw new InvalidArgumentException('La preventa debe contener al menos un producto.');
            }

            $preorder = Preorder::query()->create([
                'uuid' => Arr::get($payload, 'uuid', (string) Str::uuid()),
                'customer_id' => Arr::get($payload, 'customer_id'),
                'preorder_number' => Arr::get($payload, 'preorder_number', $this->generatePreorderNumber()),
                'status' => Arr::get($payload, 'status', Preorder::STATUS_PENDING),
                'subtotal' => 0,
                'discount' => $discount,
                'total' => 0,
                'amount_paid' => 0,
                'amount_due' => 0,
                'expected_release_date' => Arr::get($payload, 'expected_release_date')
                    ? Carbon::parse(Arr::get($payload, 'expected_release_date'))
                    : null,
                'notes' => Arr::get($payload, 'notes'),
                'source' => Arr::get($payload, 'source', Preorder::SOURCE_SERVER),
            ]);

            $subtotal = 0.0;

            foreach ($items as $item) {
                $product = ! empty($item['product_id'])
                    ? Product::query()->findOrFail($item['product_id'])
                    : null;

                $productName = $item['product_name'] ?? $product?->name;
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) ($item['unit_price'] ?? $product?->price ?? 0);
                $lineTotal = $quantity * $unitPrice;

                if (! $productName) {
                    throw new InvalidArgumentException('Cada item de preventa necesita producto o nombre snapshot.');
                }

                $preorder->items()->create([
                    'product_id' => $product?->id,
                    'product_uuid' => $product?->uuid,
                    'product_sku' => $product?->sku,
                    'product_name' => $productName,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                $subtotal += $lineTotal;
            }

            $preorder->update([
                'subtotal' => $subtotal,
                'total' => max($subtotal - $discount, 0),
            ]);

            foreach ($payments as $payment) {
                $this->registerPayment($preorder, $payment);
            }

            return $this->refreshBalances($preorder)->load(['customer', 'items.product', 'payments']);
        });
    }

    public function registerPayment(Preorder $preorder, array $payload): PreorderPayment
    {
        return DB::transaction(function () use ($preorder, $payload): PreorderPayment {
            $preorder->refresh();

            if (in_array($preorder->status, [Preorder::STATUS_CANCELLED, Preorder::STATUS_DELIVERED], true)) {
                throw new InvalidArgumentException('No se pueden registrar abonos en una preventa cancelada o entregada.');
            }

            $amount = (float) $payload['amount'];
            $newPaidAmount = (float) $preorder->payments()->sum('amount') + $amount;
            $total = (float) $preorder->total;

            if ($newPaidAmount > $total) {
                throw new InvalidArgumentException('El total abonado no puede exceder el total de la preventa.');
            }

            $payment = $preorder->payments()->create([
                'method' => $payload['method'],
                'amount' => $amount,
                'reference' => $payload['reference'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'paid_at' => $payload['paid_at'] ?? now(),
            ]);

            $this->refreshBalances($preorder);

            return $payment;
        });
    }

    public function refreshBalances(Preorder $preorder): Preorder
    {
        $preorder->refresh();

        $amountPaid = (float) $preorder->payments()->sum('amount');
        $total = (float) $preorder->total;
        $amountDue = max($total - $amountPaid, 0);

        $status = match (true) {
            $preorder->status === Preorder::STATUS_CANCELLED => Preorder::STATUS_CANCELLED,
            $preorder->status === Preorder::STATUS_DELIVERED => Preorder::STATUS_DELIVERED,
            $amountPaid <= 0 => Preorder::STATUS_PENDING,
            $amountPaid < $total => Preorder::STATUS_PARTIALLY_PAID,
            default => Preorder::STATUS_PAID,
        };

        $preorder->update([
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'status' => $status,
        ]);

        return $preorder->refresh();
    }

    protected function generatePreorderNumber(): string
    {
        $datePrefix = now()->format('Ymd');
        $lastPreorder = Preorder::query()
            ->whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $nextSequence = $lastPreorder ? ((int) substr($lastPreorder->preorder_number, -4)) + 1 : 1;

        return sprintf('PRE-%s-%04d', $datePrefix, $nextSequence);
    }
}
