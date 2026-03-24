<?php

namespace App\Services;

use App\Models\Device;
use App\Models\SyncLog;
use Illuminate\Support\Carbon;

class SyncLogService
{
    public function log(
        ?Device $device,
        string $entityType,
        ?string $entityUuid,
        string $action,
        string $status,
        ?array $payload = null,
        ?string $message = null,
        ?Carbon $syncedAt = null,
    ): SyncLog {
        return SyncLog::create([
            'device_id' => $device?->id,
            'entity_type' => $entityType,
            'entity_uuid' => $entityUuid,
            'action' => $action,
            'status' => $status,
            'payload_json' => $payload,
            'message' => $message,
            'synced_at' => $syncedAt,
        ]);
    }
}
