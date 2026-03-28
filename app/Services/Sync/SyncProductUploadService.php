<?php

namespace App\Services\Sync;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SyncProductUploadService
{
    public function upload(array $items): array
    {
        $results = [];

        foreach ($items as $entry) {
            $payload = $entry['product'] ?? [];
            $localId = $entry['local_id'] ?? null;

            try {
                $result = DB::transaction(function () use ($entry, $payload, $localId): array {
                    $product = $this->findMatchingProduct($payload);
                    $isNew = ! $product;

                    if (! $product) {
                        $product = new Product();
                        $product->uuid = (string) Str::uuid();
                    }

                    if ($product->trashed()) {
                        $product->restore();
                    }

                    $product->fill($this->mapPayload($product, $payload));
                    $product->save();

                    return [
                        'status' => $isNew ? 'created' : 'updated',
                        'local_id' => $localId,
                        'remote_id' => $product->id,
                        'product' => $this->serializeProduct($product->fresh()),
                    ];
                });

                $results[] = $result;
            } catch (Throwable $exception) {
                $results[] = [
                    'status' => 'error',
                    'local_id' => $localId,
                    'remote_id' => null,
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return $results;
    }

    private function findMatchingProduct(array $payload): ?Product
    {
        if (! empty($payload['remote_id'])) {
            $product = Product::withTrashed()->find($payload['remote_id']);

            if ($product) {
                return $product;
            }
        }

        if (! empty($payload['sku'])) {
            $product = Product::withTrashed()->where('sku', $payload['sku'])->first();

            if ($product) {
                return $product;
            }
        }

        if (! empty($payload['barcode'])) {
            return Product::withTrashed()->where('barcode', $payload['barcode'])->first();
        }

        return null;
    }

    private function mapPayload(Product $product, array $payload): array
    {
        return [
            'sku' => array_key_exists('sku', $payload) ? $payload['sku'] : $product->sku,
            'barcode' => array_key_exists('barcode', $payload) ? $payload['barcode'] : $product->barcode,
            'name' => array_key_exists('name', $payload) ? $payload['name'] : $product->name,
            'category' => array_key_exists('category', $payload) ? $payload['category'] : $product->category,
            'price' => array_key_exists('price', $payload) ? $payload['price'] : ($product->price ?? 0),
            'cost' => array_key_exists('cost', $payload) ? $payload['cost'] : ($product->cost ?? 0),
            'stock' => array_key_exists('stock', $payload) ? $payload['stock'] : ($product->stock ?? 0),
            'min_stock' => array_key_exists('min_stock', $payload) ? $payload['min_stock'] : ($product->min_stock ?? 0),
            'image_path' => array_key_exists('image', $payload) ? $payload['image'] : $product->image_path,
            'active' => array_key_exists('active', $payload) ? (bool) $payload['active'] : ($product->active ?? true),
            'product_type' => array_key_exists('product_type', $payload) ? $payload['product_type'] : ($product->product_type ?? 'normal'),
            'game' => array_key_exists('game', $payload) ? $payload['game'] : $product->game,
            'card_name' => array_key_exists('card_name', $payload) ? $payload['card_name'] : $product->card_name,
            'set_name' => array_key_exists('set_name', $payload) ? $payload['set_name'] : $product->set_name,
            'set_code' => array_key_exists('set_code', $payload) ? $payload['set_code'] : $product->set_code,
            'collector_number' => array_key_exists('collector_number', $payload) ? $payload['collector_number'] : $product->collector_number,
            'finish' => array_key_exists('finish', $payload) ? $payload['finish'] : $product->finish,
            'language' => array_key_exists('language', $payload) ? $payload['language'] : $product->language,
            'card_condition' => array_key_exists('card_condition', $payload) ? $payload['card_condition'] : $product->card_condition,
        ];
    }

    private function serializeProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'remote_id' => $product->id,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'name' => $product->name,
            'category' => $product->category,
            'price' => (float) $product->price,
            'cost' => (float) $product->cost,
            'stock' => $product->stock,
            'min_stock' => $product->min_stock,
            'active' => (int) $product->active,
            'product_type' => $product->product_type,
            'updated_at' => $product->updated_at?->toIso8601String(),
        ];
    }
}
