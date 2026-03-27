<?php

namespace App\Http\Requests\Inventory;

use App\Models\InventoryMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryMovementIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->filled('per_page') ? (int) $this->input('per_page') : 15,
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'exists:products,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'movement_type' => ['nullable', Rule::in([
                InventoryMovement::TYPE_SALE,
                InventoryMovement::TYPE_RESTOCK,
                InventoryMovement::TYPE_MANUAL_ADJUSTMENT,
                InventoryMovement::TYPE_RETURN,
                InventoryMovement::TYPE_SYNC_CORRECTION,
            ])],
            'source' => ['nullable', Rule::in([
                InventoryMovement::SOURCE_SERVER,
                InventoryMovement::SOURCE_POS,
                InventoryMovement::SOURCE_SYSTEM,
            ])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ];
    }
}
