<?php

namespace App\Http\Requests\Sync;

use App\Models\CashClosure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadCashClosuresRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $closures = collect($this->input('closures', []))
            ->filter(fn ($closure) => is_array($closure) && ! empty($closure['uuid']))
            ->values()
            ->all();

        $this->merge([
            'closures' => $closures,
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
            'closures' => ['required', 'array', 'min:1'],
            'closures.*.uuid' => ['required', 'uuid', 'distinct'],
            'closures.*.user_id' => ['nullable', 'exists:users,id'],
            'closures.*.user_uuid' => ['nullable', 'uuid'],
            'closures.*.opening_amount' => ['nullable', 'numeric', 'min:0'],
            'closures.*.cash_sales' => ['nullable', 'numeric', 'min:0'],
            'closures.*.card_sales' => ['nullable', 'numeric', 'min:0'],
            'closures.*.transfer_sales' => ['nullable', 'numeric', 'min:0'],
            'closures.*.total_sales' => ['nullable', 'numeric', 'min:0'],
            'closures.*.expected_amount' => ['nullable', 'numeric', 'min:0'],
            'closures.*.closing_amount' => ['required', 'numeric', 'min:0'],
            'closures.*.difference' => ['nullable', 'numeric'],
            'closures.*.status' => ['required', Rule::in([
                CashClosure::STATUS_OPEN,
                CashClosure::STATUS_CLOSED,
                CashClosure::STATUS_RECONCILED,
            ])],
            'closures.*.source' => ['required', Rule::in([
                CashClosure::SOURCE_SERVER,
                CashClosure::SOURCE_POS,
                CashClosure::SOURCE_SYSTEM,
            ])],
            'closures.*.notes' => ['nullable', 'string', 'max:2000'],
            'closures.*.opened_at' => ['nullable', 'date'],
            'closures.*.closed_at' => ['nullable', 'date'],
            'closures.*.client_generated_at' => ['nullable', 'date'],
            'closures.*.received_at' => ['nullable', 'date'],
        ];
    }
}
