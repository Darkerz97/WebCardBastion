<?php

namespace App\Services\Sync;

class SyncBatchResultService
{
    public function created(string $uuid, string $message, mixed $serverEntity = null): array
    {
        return $this->make($uuid, 'created', $message, $serverEntity);
    }

    public function updated(string $uuid, string $message, mixed $serverEntity = null): array
    {
        return $this->make($uuid, 'updated', $message, $serverEntity);
    }

    public function skipped(string $uuid, string $message, mixed $serverEntity = null): array
    {
        return $this->make($uuid, 'skipped', $message, $serverEntity);
    }

    public function conflict(string $uuid, string $message, array $errors = [], mixed $serverEntity = null, ?string $code = null): array
    {
        return $this->make($uuid, 'conflict', $message, $serverEntity, $errors, $code);
    }

    public function failed(string $uuid, string $message, array $errors = [], ?string $code = 'server_error'): array
    {
        return $this->make($uuid, 'failed', $message, null, $errors, $code);
    }

    public function summarize(array $items): array
    {
        $counts = collect($items)
            ->groupBy('status')
            ->map(fn ($group) => $group->count())
            ->all();

        return [
            'total' => count($items),
            'created' => $counts['created'] ?? 0,
            'updated' => $counts['updated'] ?? 0,
            'skipped' => $counts['skipped'] ?? 0,
            'conflict' => $counts['conflict'] ?? 0,
            'failed' => $counts['failed'] ?? 0,
        ];
    }

    private function make(string $uuid, string $status, string $message, mixed $serverEntity = null, array $errors = [], ?string $code = null): array
    {
        return [
            'uuid' => $uuid,
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'server_entity' => $serverEntity,
            'errors' => $errors,
        ];
    }
}
