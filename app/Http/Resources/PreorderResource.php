<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreorderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'customer_id' => $this->customer_id,
            'preorder_number' => $this->preorder_number,
            'status' => $this->status,
            'is_active' => ! in_array($this->status, [\App\Models\Preorder::STATUS_CANCELLED, \App\Models\Preorder::STATUS_DELIVERED], true),
            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,
            'amount_paid' => (float) $this->amount_paid,
            'amount_due' => (float) $this->amount_due,
            'expected_release_date' => $this->expected_release_date?->toIso8601String(),
            'notes' => $this->notes,
            'source' => $this->source,
            'sync_version' => $this->sync_version,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'items' => PreorderItemResource::collection($this->whenLoaded('items')),
            'payments' => PreorderPaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => null,
        ];
    }
}
