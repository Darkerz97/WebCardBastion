<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashClosureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'device_id' => $this->device_id,
            'user_id' => $this->user_id,
            'opening_amount' => (float) $this->opening_amount,
            'cash_sales' => (float) $this->cash_sales,
            'card_sales' => (float) $this->card_sales,
            'transfer_sales' => (float) $this->transfer_sales,
            'total_sales' => (float) $this->total_sales,
            'expected_amount' => (float) $this->expected_amount,
            'closing_amount' => (float) $this->closing_amount,
            'difference' => (float) $this->difference,
            'status' => $this->status,
            'source' => $this->source,
            'notes' => $this->notes,
            'opened_at' => $this->opened_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
            'client_generated_at' => $this->client_generated_at?->toIso8601String(),
            'received_at' => $this->received_at?->toIso8601String(),
            'sync_version' => $this->sync_version,
            'device' => new DeviceResource($this->whenLoaded('device')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => null,
        ];
    }
}
