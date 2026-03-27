<?php

namespace App\Services;

use App\Models\Device;
use App\Models\SyncLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        $log = SyncLog::create([
            'device_id' => $device?->id,
            'entity_type' => $entityType,
            'entity_uuid' => $entityUuid,
            'action' => $action,
            'status' => $status,
            'payload_json' => $payload,
            'message' => $message,
            'synced_at' => $syncedAt,
        ]);

        $context = [
            'device_code' => $device?->device_code,
            'entity_type' => $entityType,
            'entity_uuid' => $entityUuid,
            'action' => $action,
            'status' => $status,
            'message' => $message,
        ];

        match ($status) {
            'failed' => Log::error('Sync operation failed.', $context),
            'conflict' => Log::warning('Sync operation has conflicts.', $context),
            'skipped' => Log::info('Sync operation skipped by idempotency.', $context),
            default => Log::info('Sync operation processed.', $context),
        };

        return $log;
    }
}
