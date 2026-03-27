<?php

namespace App\Services;

use App\Http\Resources\InventoryMovementResource;
use App\Models\Device;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\Sync\SyncBatchResultService;
use App\Services\Sync\SyncConflictResolver;
use App\Support\SyncConflictException;
use App\Support\SyncReferenceException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class InventoryMovementService
{
    public function __construct(
        private readonly SyncLogService $syncLogService,
        private readonly SyncBatchResultService $syncBatchResultService,
        private readonly SyncConflictResolver $syncConflictResolver,
    ) {
    }

    public function createManualAdjustment(array $payload): InventoryMovement
    {
        return DB::transaction(function () use ($payload): InventoryMovement {
            $product = Product::query()->lockForUpdate()->findOrFail($payload['product_id']);
            $quantity = (int) $payload['quantity'];
            $stockBefore = (int) $product->stock;
            $stockAfter = $this->calculateStockAfter($stockBefore, $quantity, $payload['direction']);

            $product->update(['stock' => $stockAfter]);

            return InventoryMovement::query()->create([
                'uuid' => $payload['uuid'] ?? (string) Str::uuid(),
                'product_id' => $product->id,
                'sale_id' => $payload['sale_id'] ?? null,
                'device_id' => $payload['device_id'] ?? null,
                'user_id' => $payload['user_id'] ?? null,
                'movement_type' => $payload['movement_type'],
                'direction' => $payload['direction'],
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'unit_cost' => Arr::get($payload, 'unit_cost'),
                'reference' => Arr::get($payload, 'reference'),
                'notes' => Arr::get($payload, 'notes'),
                'source' => Arr::get($payload, 'source', InventoryMovement::SOURCE_SERVER),
                'occurred_at' => Carbon::parse(Arr::get($payload, 'occurred_at', now())),
                'client_generated_at' => Arr::get($payload, 'client_generated_at'),
                'received_at' => Arr::get($payload, 'received_at'),
            ])->load(['product', 'sale', 'device', 'user.role']);
        });
    }

    public function recordSaleItemMovement(Sale $sale, Product $product, SaleItem $saleItem, int $stockBefore, int $stockAfter, array $context = []): InventoryMovement
    {
        return InventoryMovement::query()->create([
            'uuid' => (string) Str::uuid(),
            'product_id' => $product->id,
            'sale_id' => $sale->id,
            'device_id' => $sale->device_id,
            'user_id' => $sale->user_id,
            'movement_type' => InventoryMovement::TYPE_SALE,
            'direction' => InventoryMovement::DIRECTION_OUT,
            'quantity' => $saleItem->quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'unit_cost' => Arr::get($context, 'unit_cost', $product->cost),
            'reference' => $sale->sale_number,
            'notes' => Arr::get($context, 'notes', 'Movimiento generado automaticamente por venta completada.'),
            'source' => Arr::get($context, 'source', InventoryMovement::SOURCE_SERVER),
            'occurred_at' => Carbon::parse($sale->sold_at ?? now()),
            'client_generated_at' => $sale->client_generated_at,
            'received_at' => $sale->received_at,
        ]);
    }

    public function uploadFromPos(?Device $device, array $movements): array
    {
        $results = [];

        foreach ($movements as $payload) {
            try {
                $existingMovement = InventoryMovement::query()->where('uuid', $payload['uuid'])->first();

                if ($existingMovement) {
                    $results[] = $this->syncBatchResultService->skipped(
                        $existingMovement->uuid,
                        'El movimiento ya habia sido sincronizado.',
                        (new InventoryMovementResource($existingMovement->loadMissing(['product', 'sale', 'device', 'user.role'])))->resolve(),
                    );

                    $this->syncLogService->log($device, 'inventory_movement', $existingMovement->uuid, 'upload', 'skipped', $payload, 'Movimiento duplicado omitido.', now());

                    continue;
                }

                $this->syncConflictResolver->ensureInventoryMovementCanBeUploaded($payload);

                if (! $device) {
                    throw new SyncReferenceException(
                        'El dispositivo emisor no existe o esta inactivo en el servidor.',
                        ['device_code' => ['No existe un dispositivo activo con el codigo enviado.']],
                        'missing_device',
                    );
                }

                $resolvedPayload = [
                    ...$this->resolveUploadPayload($payload),
                    'device_id' => $device->id,
                    'received_at' => $payload['received_at'] ?? now(),
                ];

                $existingSaleMovement = $this->findExistingSaleMovement($resolvedPayload);

                if ($existingSaleMovement) {
                    $results[] = $this->syncBatchResultService->skipped(
                        $payload['uuid'],
                        'El movimiento ya fue generado previamente por la venta asociada.',
                        (new InventoryMovementResource($existingSaleMovement->loadMissing(['product', 'sale', 'device', 'user.role'])))->resolve(),
                    );

                    $this->syncLogService->log($device, 'inventory_movement', $payload['uuid'], 'upload', 'skipped', $payload, 'Movimiento omitido por venta ya registrada.', now());

                    continue;
                }

                $movement = $this->createManualAdjustment($resolvedPayload);

                $results[] = $this->syncBatchResultService->created(
                    $movement->uuid,
                    'Movimiento sincronizado correctamente.',
                    (new InventoryMovementResource($movement))->resolve(),
                );

                $this->syncLogService->log($device, 'inventory_movement', $movement->uuid, 'upload', 'success', $payload, 'Movimiento sincronizado correctamente.', now());
            } catch (SyncConflictException $exception) {
                $existingMovement = InventoryMovement::query()->where('uuid', $payload['uuid'])->first();
                $serverEntity = $existingMovement
                    ? (new InventoryMovementResource($existingMovement->loadMissing(['product', 'sale', 'device', 'user.role'])))->resolve()
                    : null;

                $results[] = $this->syncBatchResultService->conflict(
                    $payload['uuid'],
                    $exception->getMessage(),
                    $exception->errors(),
                    $serverEntity,
                    $exception->conflictCode(),
                );

                $this->syncLogService->log($device, 'inventory_movement', $payload['uuid'], 'upload', 'conflict', $payload, $exception->getMessage(), now());
            } catch (\Throwable $exception) {
                $results[] = $this->syncBatchResultService->failed(
                    $payload['uuid'],
                    $exception->getMessage(),
                    ['inventory_movement' => ['Ocurrio un error inesperado al procesar el movimiento.']],
                );

                $this->syncLogService->log($device, 'inventory_movement', $payload['uuid'], 'upload', 'failed', $payload, $exception->getMessage(), now());
            }
        }

        return $results;
    }

    public function resolveDeviceByCode(string $deviceCode): ?Device
    {
        return Device::query()
            ->where('device_code', $deviceCode)
            ->where('active', true)
            ->first();
    }

    private function resolveUploadPayload(array $payload): array
    {
        return [
            ...$payload,
            'product_id' => $this->resolveProductId($payload),
            'sale_id' => $this->resolveSaleId($payload),
            'user_id' => $this->resolveUserId($payload),
        ];
    }

    private function resolveProductId(array $payload): int
    {
        if (! empty($payload['product_id'])) {
            return (int) $payload['product_id'];
        }

        foreach ([
            ['field' => 'product_uuid', 'column' => 'uuid'],
            ['field' => 'product_sku', 'column' => 'sku'],
            ['field' => 'product_barcode', 'column' => 'barcode'],
        ] as $resolver) {
            if (! empty($payload[$resolver['field']])) {
                $productId = Product::query()->where($resolver['column'], $payload[$resolver['field']])->value('id');

                if ($productId) {
                    return (int) $productId;
                }
            }
        }

        throw new SyncReferenceException(
            'No fue posible resolver el producto del movimiento.',
            ['product' => ['Debes enviar una referencia de producto valida.']],
            'missing_product',
        );
    }

    private function resolveSaleId(array $payload): ?int
    {
        if (! empty($payload['sale_id'])) {
            return (int) $payload['sale_id'];
        }

        if (! empty($payload['sale_uuid'])) {
            $saleId = Sale::query()->where('uuid', $payload['sale_uuid'])->value('id');

            if ($saleId) {
                return (int) $saleId;
            }

            throw new SyncReferenceException(
                'La venta indicada por UUID no existe en el servidor.',
                ['sale_uuid' => ['No existe una venta con ese UUID.']],
                'missing_sale',
            );
        }

        return null;
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

    private function calculateStockAfter(int $stockBefore, int $quantity, string $direction): int
    {
        return match ($direction) {
            InventoryMovement::DIRECTION_IN => $stockBefore + $quantity,
            InventoryMovement::DIRECTION_OUT => $this->decreaseStock($stockBefore, $quantity),
            InventoryMovement::DIRECTION_ADJUSTMENT => max(0, $quantity),
            default => throw new InvalidArgumentException('Direccion de movimiento invalida.'),
        };
    }

    private function decreaseStock(int $stockBefore, int $quantity): int
    {
        if ($stockBefore < $quantity) {
            throw new InvalidArgumentException('Stock insuficiente para registrar el movimiento.');
        }

        return $stockBefore - $quantity;
    }

    private function findExistingSaleMovement(array $payload): ?InventoryMovement
    {
        if (($payload['movement_type'] ?? null) !== InventoryMovement::TYPE_SALE || empty($payload['sale_id']) || empty($payload['product_id'])) {
            return null;
        }

        return InventoryMovement::query()
            ->where('sale_id', $payload['sale_id'])
            ->where('product_id', $payload['product_id'])
            ->where('movement_type', InventoryMovement::TYPE_SALE)
            ->where('direction', $payload['direction'])
            ->where('quantity', $payload['quantity'])
            ->first();
    }
}
