<?php

namespace App\Http\Requests\CashClosure;

use App\Models\CashClosure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashClosureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id' => ['nullable', 'exists:devices,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'opening_amount' => ['nullable', 'numeric', 'min:0'],
            'cash_sales' => ['nullable', 'numeric', 'min:0'],
            'card_sales' => ['nullable', 'numeric', 'min:0'],
            'transfer_sales' => ['nullable', 'numeric', 'min:0'],
            'total_sales' => ['nullable', 'numeric', 'min:0'],
            'expected_amount' => ['nullable', 'numeric', 'min:0'],
            'closing_amount' => ['required', 'numeric', 'min:0'],
            'difference' => ['nullable', 'numeric'],
            'status' => ['required', Rule::in([
                CashClosure::STATUS_OPEN,
                CashClosure::STATUS_CLOSED,
                CashClosure::STATUS_RECONCILED,
            ])],
            'source' => ['nullable', Rule::in([
                CashClosure::SOURCE_SERVER,
                CashClosure::SOURCE_POS,
                CashClosure::SOURCE_SYSTEM,
            ])],
            'notes' => ['nullable', 'string', 'max:2000'],
            'opened_at' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date'],
        ];
    }
}
