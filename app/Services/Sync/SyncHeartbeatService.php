<?php

namespace App\Services\Sync;

use App\Models\Device;
use App\Services\SyncLogService;
use Illuminate\Support\Str;

class SyncHeartbeatService
{
    public function __construct(private readonly SyncLogService $syncLogService)
    {
    }

    public function register(array $payload): Device
    {
        $device = Device::query()->firstOrNew([
            'device_code' => $payload['device_code'],
        ]);

        $device->fill([
            'uuid' => $device->uuid ?: (string) Str::uuid(),
            'name' => $payload['name'],
            'type' => $payload['type'],
            'active' => true,
            'last_seen_at' => now(),
        ])->save();

        $this->syncLogService->log(
            device: $device,
            entityType: 'device',
            entityUuid: $device->uuid,
            action: 'heartbeat',
            status: 'received',
            payload: $payload,
            message: 'Heartbeat recibido correctamente.',
            syncedAt: now(),
        );

        return $device->refresh();
    }
}
