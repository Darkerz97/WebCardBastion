<?php

namespace App\Http\Requests\Inventory;

use App\Models\InventoryMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'sale_id' => ['nullable', 'exists:sales,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'movement_type' => ['required', Rule::in([
                InventoryMovement::TYPE_RESTOCK,
                InventoryMovement::TYPE_MANUAL_ADJUSTMENT,
                InventoryMovement::TYPE_RETURN,
                InventoryMovement::TYPE_SYNC_CORRECTION,
            ])],
            'direction' => ['required', Rule::in([
                InventoryMovement::DIRECTION_IN,
                InventoryMovement::DIRECTION_OUT,
                InventoryMovement::DIRECTION_ADJUSTMENT,
            ])],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', Rule::in([
                InventoryMovement::SOURCE_SERVER,
                InventoryMovement::SOURCE_POS,
                InventoryMovement::SOURCE_SYSTEM,
            ])],
            'occurred_at' => ['nullable', 'date'],
        ];
    }
}
