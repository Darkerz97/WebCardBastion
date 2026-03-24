<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SyncLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'entity_type' => $this->entity_type,
            'entity_uuid' => $this->entity_uuid,
            'action' => $this->action,
            'status' => $this->status,
            'payload_json' => $this->payload_json,
            'message' => $this->message,
            'synced_at' => $this->synced_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
