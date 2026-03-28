<?php

namespace App\Http\Requests\Preorder;

use App\Models\Preorder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePreorderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                Preorder::STATUS_PENDING,
                Preorder::STATUS_PARTIALLY_PAID,
                Preorder::STATUS_PAID,
                Preorder::STATUS_CANCELLED,
                Preorder::STATUS_DELIVERED,
            ])],
        ];
    }
}
