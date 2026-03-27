<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'customer_id' => $this->customer_id,
            'user_id' => $this->user_id,
            'device_id' => $this->device_id,
            'sale_number' => $this->sale_number,
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,
            'status' => $this->status,
            'is_active' => $this->status !== \App\Models\Sale::STATUS_CANCELLED,
            'payment_status' => $this->payment_status,
            'sync_version' => $this->sync_version,
            'sold_at' => $this->sold_at?->toIso8601String(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'user' => new UserResource($this->whenLoaded('user')),
            'device' => new DeviceResource($this->whenLoaded('device')),
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => null,
        ];
    }
}
