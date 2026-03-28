<?php

namespace App\Http\Requests\Preorder;

use App\Models\Payment;
use App\Models\Preorder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePreorderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn (array $item) => ! empty($item['quantity']) && (! empty($item['product_id']) || ! empty($item['product_name'])))
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
            'uuid' => ['nullable', 'uuid', 'unique:preorders,uuid'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'preorder_number' => ['nullable', 'string', 'max:100', 'unique:preorders,preorder_number'],
            'status' => ['nullable', Rule::in([
                Preorder::STATUS_PENDING,
                Preorder::STATUS_PARTIALLY_PAID,
                Preorder::STATUS_PAID,
                Preorder::STATUS_CANCELLED,
                Preorder::STATUS_DELIVERED,
            ])],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'expected_release_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'source' => ['nullable', Rule::in([
                Preorder::SOURCE_SERVER,
                Preorder::SOURCE_POS,
                Preorder::SOURCE_WEB,
            ])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.product_name' => ['nullable', 'string', 'max:255'],
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
