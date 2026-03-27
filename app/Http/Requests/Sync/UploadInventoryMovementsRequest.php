<?php

namespace App\Http\Requests\Sync;

use App\Models\InventoryMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadInventoryMovementsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $movements = collect($this->input('movements', []))
            ->filter(fn ($movement) => is_array($movement) && ! empty($movement['uuid']))
            ->values()
            ->all();

        $this->merge([
            'movements' => $movements,
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
            'movements' => ['required', 'array', 'min:1'],
            'movements.*.uuid' => ['required', 'uuid', 'distinct'],
            'movements.*.product_id' => ['nullable', 'exists:products,id'],
            'movements.*.product_uuid' => ['nullable', 'uuid'],
            'movements.*.product_sku' => ['nullable', 'string', 'max:100'],
            'movements.*.product_barcode' => ['nullable', 'string', 'max:100'],
            'movements.*.sale_id' => ['nullable', 'exists:sales,id'],
            'movements.*.sale_uuid' => ['nullable', 'uuid'],
            'movements.*.user_id' => ['nullable', 'exists:users,id'],
            'movements.*.user_uuid' => ['nullable', 'uuid'],
            'movements.*.movement_type' => ['required', Rule::in([
                InventoryMovement::TYPE_SALE,
                InventoryMovement::TYPE_RESTOCK,
                InventoryMovement::TYPE_MANUAL_ADJUSTMENT,
                InventoryMovement::TYPE_RETURN,
                InventoryMovement::TYPE_SYNC_CORRECTION,
            ])],
            'movements.*.direction' => ['required', Rule::in([
                InventoryMovement::DIRECTION_IN,
                InventoryMovement::DIRECTION_OUT,
                InventoryMovement::DIRECTION_ADJUSTMENT,
            ])],
            'movements.*.quantity' => ['required', 'integer', 'min:0'],
            'movements.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
            'movements.*.reference' => ['nullable', 'string', 'max:255'],
            'movements.*.notes' => ['nullable', 'string', 'max:2000'],
            'movements.*.source' => ['required', Rule::in([
                InventoryMovement::SOURCE_SERVER,
                InventoryMovement::SOURCE_POS,
                InventoryMovement::SOURCE_SYSTEM,
            ])],
            'movements.*.occurred_at' => ['nullable', 'date'],
        ];
    }
}
