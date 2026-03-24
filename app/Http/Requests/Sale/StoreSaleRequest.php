<?php

namespace App\Http\Requests\Sale;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn (array $item) => ! empty($item['product_id']) && ! empty($item['quantity']))
            ->values()
            ->all();

        $payments = collect($this->input('payments', []))
            ->filter(fn (array $payment) => ! empty($payment['method']) && ! empty($payment['amount']))
            ->values()
            ->all();

        $this->merge([
            'items' => $items,
            'payments' => $payments,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid' => ['nullable', 'uuid', 'unique:sales,uuid'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'sale_number' => ['nullable', 'string', 'max:100', 'unique:sales,sale_number'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in([
                Sale::STATUS_DRAFT,
                Sale::STATUS_COMPLETED,
                Sale::STATUS_CANCELLED,
            ])],
            'sold_at' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'payments' => ['nullable', 'array'],
            'payments.*.method' => ['required_with:payments', Rule::in([
                Payment::METHOD_CASH,
                Payment::METHOD_CARD,
                Payment::METHOD_TRANSFER,
                Payment::METHOD_CREDIT,
                Payment::METHOD_MIXED,
            ])],
            'payments.*.amount' => ['required_with:payments', 'numeric', 'min:0.01'],
            'payments.*.reference' => ['nullable', 'string', 'max:255'],
            'payments.*.notes' => ['nullable', 'string'],
            'payments.*.paid_at' => ['nullable', 'date'],
        ];
    }
}
