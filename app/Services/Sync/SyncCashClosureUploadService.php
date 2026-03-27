<?php

namespace App\Services\Sync;

use App\Http\Resources\CashClosureResource;
use App\Models\CashClosure;
use App\Models\Device;
use App\Models\User;
use App\Services\SyncLogService;
use App\Support\SyncConflictException;
use App\Support\SyncReferenceException;
use Illuminate\Support\Carbon;
use Throwable;

class SyncCashClosureUploadService
{
    public function __construct(
        private readonly SyncLogService $syncLogService,
        private readonly SyncBatchResultService $syncBatchResultService,
        private readonly SyncConflictResolver $syncConflictResolver,
    ) {
    }

    public function upload(?Device $device, array $closures): array
    {
        $results = [];

        foreach ($closures as $payload) {
            try {
                $existingClosure = CashClosure::query()->where('uuid', $payload['uuid'])->first();

                if ($existingClosure) {
                    $results[] = $this->syncBatchResultService->skipped(
                        $existingClosure->uuid,
                        'El cierre de caja ya habia sido sincronizado.',
                        (new CashClosureResource($existingClosure->loadMissing(['device', 'user.role'])))->resolve(),
                    );

                    $this->syncLogService->log($device, 'cash_closure', $existingClosure->uuid, 'upload', 'skipped', $payload, 'Cierre duplicado omitido.', now());

                    continue;
                }

                $this->syncConflictResolver->ensureCashClosureCanBeUploaded($payload);

                if (! $device) {
                    throw new SyncReferenceException(
                        'El dispositivo emisor no existe o esta inactivo en el servidor.',
                        ['device_code' => ['No existe un dispositivo activo con el codigo enviado.']],
                        'missing_device',
                    );
                }

                $closure = CashClosure::query()->create([
                    'uuid' => $payload['uuid'],
                    'device_id' => $device->id,
                    'user_id' => $this->resolveUserId($payload),
                    'opening_amount' => $payload['opening_amount'] ?? 0,
                    'cash_sales' => $payload['cash_sales'] ?? 0,
                    'card_sales' => $payload['card_sales'] ?? 0,
                    'transfer_sales' => $payload['transfer_sales'] ?? 0,
                    'total_sales' => $payload['total_sales'] ?? $this->calculateTotalSales($payload),
                    'expected_amount' => $payload['expected_amount'] ?? (($payload['opening_amount'] ?? 0) + ($payload['cash_sales'] ?? 0)),
                    'closing_amount' => $payload['closing_amount'],
                    'difference' => $payload['difference'] ?? ($payload['closing_amount'] - (($payload['expected_amount'] ?? (($payload['opening_amount'] ?? 0) + ($payload['cash_sales'] ?? 0))))),
                    'status' => $payload['status'],
                    'source' => $payload['source'],
                    'notes' => $payload['notes'] ?? null,
                    'opened_at' => isset($payload['opened_at']) ? Carbon::parse($payload['opened_at']) : null,
                    'closed_at' => isset($payload['closed_at']) ? Carbon::parse($payload['closed_at']) : now(),
                    'client_generated_at' => isset($payload['client_generated_at']) ? Carbon::parse($payload['client_generated_at']) : null,
                    'received_at' => isset($payload['received_at']) ? Carbon::parse($payload['received_at']) : now(),
                ])->load(['device', 'user.role']);

                $results[] = $this->syncBatchResultService->created(
                    $closure->uuid,
                    'Cierre de caja sincronizado correctamente.',
                    (new CashClosureResource($closure))->resolve(),
                );

                $this->syncLogService->log($device, 'cash_closure', $closure->uuid, 'upload', 'success', $payload, 'Cierre de caja sincronizado correctamente.', now());
            } catch (SyncConflictException $exception) {
                $existingClosure = CashClosure::query()->where('uuid', $payload['uuid'])->first();
                $serverEntity = $existingClosure
                    ? (new CashClosureResource($existingClosure->loadMissing(['device', 'user.role'])))->resolve()
                    : null;

                $results[] = $this->syncBatchResultService->conflict(
                    $payload['uuid'],
                    $exception->getMessage(),
                    $exception->errors(),
                    $serverEntity,
                    $exception->conflictCode(),
                );

                $this->syncLogService->log($device, 'cash_closure', $payload['uuid'], 'upload', 'conflict', $payload, $exception->getMessage(), now());
            } catch (Throwable $exception) {
                $results[] = $this->syncBatchResultService->failed(
                    $payload['uuid'],
                    $exception->getMessage(),
                    ['cash_closure' => ['Ocurrio un error inesperado al procesar el cierre de caja.']],
                );

                $this->syncLogService->log($device, 'cash_closure', $payload['uuid'], 'upload', 'failed', $payload, $exception->getMessage(), now());
            }
        }

        return $results;
    }

    public function resolveDevice(string $deviceCode): ?Device
    {
        return Device::query()
            ->where('device_code', $deviceCode)
            ->where('active', true)
            ->first();
    }

    private function resolveUserId(array $payload): ?int
    {
        if (! empty($payload['user_id'])) {
            return (int) $payload['user_id'];
        }

        if (! empty($payload['user_uuid'])) {
            $userId = User::query()->where('uuid', $payload['user_uuid'])->value('id');

            if ($userId) {
                return (int) $userId;
            }

            throw new SyncReferenceException(
                'El usuario indicado por UUID no existe en el servidor.',
                ['user_uuid' => ['No existe un usuario con ese UUID.']],
                'missing_user',
            );
        }

        return null;
    }

    private function calculateTotalSales(array $payload): float
    {
        return (float) ($payload['cash_sales'] ?? 0)
            + (float) ($payload['card_sales'] ?? 0)
            + (float) ($payload['transfer_sales'] ?? 0);
    }
}
