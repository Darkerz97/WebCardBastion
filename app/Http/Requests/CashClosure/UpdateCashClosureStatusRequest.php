<?php

namespace App\Http\Requests\CashClosure;

use App\Models\CashClosure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCashClosureStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                CashClosure::STATUS_OPEN,
                CashClosure::STATUS_CLOSED,
                CashClosure::STATUS_RECONCILED,
            ])],
        ];
    }
}
