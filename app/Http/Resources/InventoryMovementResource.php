<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'product_id' => $this->product_id,
            'sale_id' => $this->sale_id,
            'device_id' => $this->device_id,
            'user_id' => $this->user_id,
            'movement_type' => $this->movement_type,
            'direction' => $this->direction,
            'quantity' => $this->quantity,
            'stock_before' => $this->stock_before,
            'stock_after' => $this->stock_after,
            'unit_cost' => $this->unit_cost !== null ? (float) $this->unit_cost : null,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'source' => $this->source,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'sync_version' => $this->sync_version,
            'product' => new ProductResource($this->whenLoaded('product')),
            'sale' => new SaleResource($this->whenLoaded('sale')),
            'device' => new DeviceResource($this->whenLoaded('device')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
