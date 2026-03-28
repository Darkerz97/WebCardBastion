<?php

namespace App\Http\Requests\Sync;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $products = collect($this->input('products', []))
            ->filter(fn ($entry) => is_array($entry) && is_array($entry['product'] ?? null))
            ->values()
            ->all();

        $this->merge([
            'products' => $products,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['required', 'string', 'max:100'],
            'device_code' => ['required', 'string', 'max:100'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.local_id' => ['nullable'],
            'products.*.event_type' => ['nullable', 'string', 'max:100'],
            'products.*.action' => ['nullable', 'string', 'max:50'],
            'products.*.product' => ['required', 'array'],
            'products.*.product.remote_id' => ['nullable', 'integer'],
            'products.*.product.sku' => ['nullable', 'string', 'max:100'],
            'products.*.product.barcode' => ['nullable', 'string', 'max:100'],
            'products.*.product.name' => ['required', 'string', 'max:255'],
            'products.*.product.category' => ['nullable', 'string', 'max:100'],
            'products.*.product.price' => ['nullable', 'numeric', 'min:0'],
            'products.*.product.cost' => ['nullable', 'numeric', 'min:0'],
            'products.*.product.stock' => ['nullable', 'integer', 'min:0'],
            'products.*.product.min_stock' => ['nullable', 'integer', 'min:0'],
            'products.*.product.image' => ['nullable', 'string', 'max:255'],
            'products.*.product.active' => ['nullable', 'boolean'],
            'products.*.product.product_type' => ['nullable', 'string', 'max:100'],
            'products.*.product.game' => ['nullable', 'string', 'max:255'],
            'products.*.product.card_name' => ['nullable', 'string', 'max:255'],
            'products.*.product.set_name' => ['nullable', 'string', 'max:255'],
            'products.*.product.set_code' => ['nullable', 'string', 'max:100'],
            'products.*.product.collector_number' => ['nullable', 'string', 'max:100'],
            'products.*.product.finish' => ['nullable', 'string', 'max:100'],
            'products.*.product.language' => ['nullable', 'string', 'max:100'],
            'products.*.product.card_condition' => ['nullable', 'string', 'max:100'],
            'products.*.product.created_at' => ['nullable', 'date'],
            'products.*.product.updated_at' => ['nullable', 'date'],
        ];
    }
}
