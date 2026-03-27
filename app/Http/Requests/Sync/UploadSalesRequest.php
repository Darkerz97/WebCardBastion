<?php

namespace App\Http\Requests\Sync;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadSalesRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $sales = collect($this->input('sales', []))
            ->filter(fn ($sale) => is_array($sale) && ! empty($sale['uuid']))
            ->map(function (array $sale): array {
                $sale['items'] = collect($sale['items'] ?? [])
                    ->filter(fn ($item) => is_array($item) && ! empty($item['quantity']))
                    ->values()
                    ->all();

                $sale['payments'] = collect($sale['payments'] ?? [])
                    ->filter(fn ($payment) => is_array($payment) && ! empty($payment['method']) && ! empty($payment['amount']))
                    ->values()
                    ->all();

                return $sale;
            })
            ->values()
            ->all();

        $this->merge([
            'sales' => $sales,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_code' => ['required', 'string', 'max:100'],
            'sales' => ['required', 'array', 'min:1'],
            'sales.*.uuid' => ['required', 'uuid', 'distinct'],
            'sales.*.customer_id' => ['nullable', 'exists:customers,id'],
            'sales.*.customer_uuid' => ['nullable', 'uuid'],
            'sales.*.customer_email' => ['nullable', 'email'],
            'sales.*.customer_phone' => ['nullable', 'string', 'max:100'],
            'sales.*.user_id' => ['nullable', 'exists:users,id'],
            'sales.*.user_uuid' => ['nullable', 'uuid'],
            'sales.*.sale_number' => ['nullable', 'string', 'max:100'],
            'sales.*.discount' => ['nullable', 'numeric', 'min:0'],
            'sales.*.status' => ['required', Rule::in([
                Sale::STATUS_DRAFT,
                Sale::STATUS_COMPLETED,
                Sale::STATUS_CANCELLED,
            ])],
            'sales.*.sold_at' => ['nullable', 'date'],
            'sales.*.client_generated_at' => ['nullable', 'date'],
            'sales.*.received_at' => ['nullable', 'date'],
            'sales.*.items' => ['required', 'array', 'min:1'],
            'sales.*.items.*.product_id' => ['nullable', 'exists:products,id', 'required_without_all:sales.*.items.*.product_uuid,sales.*.items.*.product_sku,sales.*.items.*.product_barcode'],
            'sales.*.items.*.product_uuid' => ['nullable', 'uuid', 'required_without_all:sales.*.items.*.product_id,sales.*.items.*.product_sku,sales.*.items.*.product_barcode'],
            'sales.*.items.*.product_sku' => ['nullable', 'string', 'max:100', 'required_without_all:sales.*.items.*.product_id,sales.*.items.*.product_uuid,sales.*.items.*.product_barcode'],
            'sales.*.items.*.product_barcode' => ['nullable', 'string', 'max:100', 'required_without_all:sales.*.items.*.product_id,sales.*.items.*.product_uuid,sales.*.items.*.product_sku'],
            'sales.*.items.*.quantity' => ['required', 'integer', 'min:1'],
            'sales.*.items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'sales.*.payments' => ['nullable', 'array'],
            'sales.*.payments.*.method' => ['required_with:sales.*.payments', Rule::in([
                Payment::METHOD_CASH,
                Payment::METHOD_CARD,
                Payment::METHOD_TRANSFER,
                Payment::METHOD_CREDIT,
                Payment::METHOD_MIXED,
            ])],
            'sales.*.payments.*.amount' => ['required_with:sales.*.payments', 'numeric', 'min:0.01'],
            'sales.*.payments.*.reference' => ['nullable', 'string', 'max:255'],
            'sales.*.payments.*.notes' => ['nullable', 'string'],
            'sales.*.payments.*.paid_at' => ['nullable', 'date'],
        ];
    }
}
