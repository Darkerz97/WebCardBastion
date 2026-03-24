<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'store_cart';

    public function items(): Collection
    {
        $cart = collect(session(self::SESSION_KEY, []));

        if ($cart->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->with('categoryModel')
            ->published()
            ->whereIn('id', $cart->keys())
            ->get()
            ->keyBy('id');

        return $cart->map(function (array $line, int|string $productId) use ($products): ?array {
            $product = $products->get((int) $productId);

            if (! $product) {
                return null;
            }

            $quantity = max(1, min((int) $line['quantity'], $product->stock ?: 1));
            $unitPrice = (float) $product->price;

            return [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $quantity * $unitPrice,
            ];
        })->filter()->values();
    }

    public function add(Product $product, int $quantity = 1): void
    {
        if ($product->stock <= 0) {
            return;
        }

        $cart = session(self::SESSION_KEY, []);
        $existingQuantity = (int) ($cart[$product->id]['quantity'] ?? 0);

        $cart[$product->id] = [
            'quantity' => max(1, min($existingQuantity + $quantity, $product->stock)),
        ];

        session([self::SESSION_KEY => $cart]);
    }

    public function update(Product $product, int $quantity): void
    {
        $cart = session(self::SESSION_KEY, []);

        if ($quantity <= 0) {
            unset($cart[$product->id]);
        } else {
            if ($product->stock <= 0) {
                unset($cart[$product->id]);
                session([self::SESSION_KEY => $cart]);

                return;
            }

            $cart[$product->id] = [
                'quantity' => max(1, min($quantity, $product->stock)),
            ];
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function remove(Product $product): void
    {
        $cart = session(self::SESSION_KEY, []);
        unset($cart[$product->id]);

        session([self::SESSION_KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('line_total');
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
