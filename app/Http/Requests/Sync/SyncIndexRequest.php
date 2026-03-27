<?php

namespace App\Http\Requests\Sync;

use Illuminate\Foundation\Http\FormRequest;

class SyncIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->filled('per_page') ? (int) $this->input('per_page') : null,
            'include_deleted' => $this->boolean('include_deleted'),
        ]);
    }

    public function rules(): array
    {
        return [
            'updated_since' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
            'cursor' => ['nullable', 'string', 'max:2048'],
            'include_deleted' => ['nullable', 'boolean'],
        ];
    }
}
