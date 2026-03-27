<?php

namespace App\Http\Requests\Sync;

use Illuminate\Validation\Rule;

class SyncCatalogRequest extends SyncIndexRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $include = $this->input('include', []);

        if (is_string($include)) {
            $include = array_filter(array_map('trim', explode(',', $include)));
        }

        $this->merge([
            'include' => is_array($include) ? array_values($include) : [],
        ]);
    }

    public function rules(): array
    {
        return [
            ...parent::rules(),
            'include' => ['nullable', 'array'],
            'include.*' => ['string', Rule::in(['products', 'categories', 'customers', 'settings'])],
        ];
    }
}
