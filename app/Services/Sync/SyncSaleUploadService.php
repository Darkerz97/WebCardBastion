<?php

namespace App\Services\Sync;

use App\Http\Resources\SaleResource;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use App\Services\SyncLogService;
use App\Support\SyncConflictException;
use App\Support\SyncReferenceException;
use Throwable;

class SyncSaleUploadService
{
    public function __construct(
        private readonly SaleService $saleService,
        private readonly SyncLogService $syncLogService,
        private readonly SyncBatchResultService $syncBatchResultService,
        private readonly SyncConflictResolver $syncConflictResolver,
    ) {
    }

    public function upload(?Device $device, array $sales): array
    {
        $results = [];

        foreach ($sales as $payload) {
            try {
                $existingSale = Sale::query()->where('uuid', $payload['uuid'])->first();

                if ($existingSale) {
                    $results[] = $this->syncBatchResultService->skipped(
                        $existingSale->uuid,
                        'La venta ya habia sido sincronizada.',
                        (new SaleResource($existingSale->loadMissing(['customer', 'user.role', 'device', 'items.product', 'payments'])))->resolve(),
                    );

                    $this->syncLogService->log($device, 'sale', $existingSale->uuid, 'upload', 'skipped', $payload, 'Venta duplicada omitida.', now());

                    continue;
                }

                $this->syncConflictResolver->ensureSaleCanBeUploaded($payload);

                if (! $device) {
                    throw new SyncReferenceException(
                        'El dispositivo emisor no existe o esta inactivo en el servidor.',
                        ['device_code' => ['No existe un dispositivo activo con el codigo enviado.']],
                        'missing_device',
                    );
                }

                $sale = $this->saleService->create([
                    ...$this->resolveSyncPayload($payload),
                    'device_id' => $device->id,
                    'client_generated_at' => $payload['client_generated_at'] ?? null,
                    'received_at' => $payload['received_at'] ?? now(),
                ]);

                $results[] = $this->syncBatchResultService->created(
                    $sale->uuid,
                    'Venta sincronizada correctamente.',
                    (new SaleResource($sale))->resolve(),
                );

                $this->syncLogService->log($device, 'sale', $sale->uuid, 'upload', 'success', $payload, 'Venta sincronizada correctamente.', now());
            } catch (SyncConflictException $exception) {
                $existingSale = Sale::query()->where('uuid', $payload['uuid'])->first();
                $serverEntity = $existingSale
                    ? (new SaleResource($existingSale->loadMissing(['customer', 'user.role', 'device', 'items.product', 'payments'])))->resolve()
                    : null;

                $results[] = $this->syncBatchResultService->conflict(
                    $payload['uuid'],
                    $exception->getMessage(),
                    $exception->errors(),
                    $serverEntity,
                    $exception->conflictCode(),
                );

                $this->syncLogService->log($device, 'sale', $payload['uuid'], 'upload', 'conflict', $payload, $exception->getMessage(), now());
            } catch (Throwable $exception) {
                $results[] = $this->syncBatchResultService->failed(
                    $payload['uuid'],
                    $exception->getMessage(),
                    ['sale' => ['Ocurrio un error inesperado al procesar la venta.']],
                );

                $this->syncLogService->log($device, 'sale', $payload['uuid'], 'upload', 'failed', $payload, $exception->getMessage(), now());
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

    private function resolveSyncPayload(array $payload): array
    {
        return [
            ...$payload,
            'customer_id' => $this->resolveCustomerId($payload),
            'user_id' => $this->resolveUserId($payload),
            'items' => collect($payload['items'] ?? [])
                ->map(fn (array $item): array => [
                    ...$item,
                    'product_id' => $this->resolveProductId($item),
                ])
                ->all(),
        ];
    }

    private function resolveCustomerId(array $payload): ?int
    {
        if (! empty($payload['customer_id'])) {
            return (int) $payload['customer_id'];
        }

        if (! empty($payload['customer_uuid'])) {
            $customerId = Customer::query()->where('uuid', $payload['customer_uuid'])->value('id');

            if ($customerId) {
                return (int) $customerId;
            }

            throw new SyncReferenceException(
                'El cliente indicado por UUID no existe en el servidor.',
                ['customer_uuid' => ['No existe un cliente con ese UUID.']],
                'missing_customer',
            );
        }

        if (! empty($payload['customer_email'])) {
            $customerId = Customer::query()->where('email', $payload['customer_email'])->value('id');

            if ($customerId) {
                return (int) $customerId;
            }

            throw new SyncReferenceException(
                'El cliente indicado por email no existe en el servidor.',
                ['customer_email' => ['No existe un cliente con ese email.']],
                'missing_customer',
            );
        }

        if (! empty($payload['customer_phone'])) {
            $customerId = Customer::query()->where('phone', $payload['customer_phone'])->value('id');

            if ($customerId) {
                return (int) $customerId;
            }

            throw new SyncReferenceException(
                'El cliente indicado por telefono no existe en el servidor.',
                ['customer_phone' => ['No existe un cliente con ese telefono.']],
                'missing_customer',
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

    private function resolveProductId(array $item): int
    {
        if (! empty($item['product_id'])) {
            return (int) $item['product_id'];
        }

        if (! empty($item['product_uuid'])) {
            $productId = Product::query()->where('uuid', $item['product_uuid'])->value('id');

            if ($productId) {
                return (int) $productId;
            }

            throw new SyncReferenceException(
                'El producto indicado por UUID no existe en el servidor.',
                ['product_uuid' => ['No existe un producto con ese UUID.']],
                'missing_product',
            );
        }

        if (! empty($item['product_sku'])) {
            $productId = Product::query()->where('sku', $item['product_sku'])->value('id');

            if ($productId) {
                return (int) $productId;
            }

            throw new SyncReferenceException(
                'El producto indicado por SKU no existe en el servidor.',
                ['product_sku' => ['No existe un producto con ese SKU.']],
                'missing_product',
            );
        }

        if (! empty($item['product_barcode'])) {
            $productId = Product::query()->where('barcode', $item['product_barcode'])->value('id');

            if ($productId) {
                return (int) $productId;
            }

            throw new SyncReferenceException(
                'El producto indicado por barcode no existe en el servidor.',
                ['product_barcode' => ['No existe un producto con ese barcode.']],
                'missing_product',
            );
        }

        throw new SyncReferenceException(
            'No fue posible resolver el producto enviado por el POS.',
            ['product' => ['Debes enviar una referencia de producto valida.']],
            'missing_product',
        );
    }
}
