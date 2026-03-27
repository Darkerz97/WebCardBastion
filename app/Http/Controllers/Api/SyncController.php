<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sync\HeartbeatRequest;
use App\Http\Requests\Sync\UploadSalesRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\DeviceResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SaleResource;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use App\Services\SyncLogService;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class SyncController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SaleService $saleService,
        private readonly SyncLogService $syncLogService,
    ) {
    }

    public function products(Request $request): JsonResponse
    {
        $products = Product::query()
            ->when($request->filled('updated_since'), fn ($query) => $query->where('updated_at', '>=', $request->date('updated_since')))
            ->orderBy('updated_at')
            ->get();

        return $this->successResponse(ProductResource::collection($products), 'Productos listos para sincronizacion.');
    }

    public function customers(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->when($request->filled('updated_since'), fn ($query) => $query->where('updated_at', '>=', $request->date('updated_since')))
            ->orderBy('updated_at')
            ->get();

        return $this->successResponse(CustomerResource::collection($customers), 'Clientes listos para sincronizacion.');
    }

    public function heartbeat(HeartbeatRequest $request): JsonResponse
    {
        $device = Device::query()->firstOrNew([
            'device_code' => $request->string('device_code')->toString(),
        ]);

        $device->fill([
            'uuid' => $device->uuid ?: (string) Str::uuid(),
            'name' => $request->string('name')->toString(),
            'type' => $request->string('type')->toString(),
            'active' => true,
            'last_seen_at' => now(),
        ])->save();

        $this->syncLogService->log(
            device: $device,
            entityType: 'device',
            entityUuid: $device->uuid,
            action: 'heartbeat',
            status: 'received',
            payload: $request->validated(),
            message: 'Heartbeat recibido correctamente.',
            syncedAt: now(),
        );

        return $this->successResponse(new DeviceResource($device), 'Heartbeat registrado correctamente.');
    }

    public function uploadSales(UploadSalesRequest $request): JsonResponse
    {
        $device = Device::query()
            ->where('device_code', $request->string('device_code'))
            ->where('active', true)
            ->first();

        $results = [];

        foreach ($request->validated('sales') as $payload) {
            $existingSale = Sale::query()->where('uuid', $payload['uuid'])->first();

            if ($existingSale) {
                $results[] = [
                    'uuid' => $existingSale->uuid,
                    'status' => 'skipped',
                    'message' => 'La venta ya habia sido sincronizada.',
                ];

                $this->syncLogService->log($device, 'sale', $existingSale->uuid, 'upload', 'skipped', $payload, 'Venta duplicada omitida.', now());

                continue;
            }

            try {
                $sale = $this->saleService->create([
                    ...$this->resolveSyncPayload($payload),
                    'device_id' => $device?->id,
                ]);

                $results[] = [
                    'uuid' => $sale->uuid,
                    'status' => 'created',
                    'sale' => new SaleResource($sale),
                ];

                $this->syncLogService->log($device, 'sale', $sale->uuid, 'upload', 'success', $payload, 'Venta sincronizada correctamente.', now());
            } catch (Throwable $exception) {
                $results[] = [
                    'uuid' => $payload['uuid'],
                    'status' => 'failed',
                    'message' => $exception->getMessage(),
                ];

                $this->syncLogService->log($device, 'sale', $payload['uuid'], 'upload', 'failed', $payload, $exception->getMessage(), now());
            }
        }

        return $this->successResponse($results, 'Proceso de sincronizacion completado.');
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
        }

        if (! empty($payload['customer_email'])) {
            $customerId = Customer::query()->where('email', $payload['customer_email'])->value('id');

            if ($customerId) {
                return (int) $customerId;
            }
        }

        if (! empty($payload['customer_phone'])) {
            $customerId = Customer::query()->where('phone', $payload['customer_phone'])->value('id');

            if ($customerId) {
                return (int) $customerId;
            }
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
        }

        if (! empty($item['product_sku'])) {
            $productId = Product::query()->where('sku', $item['product_sku'])->value('id');

            if ($productId) {
                return (int) $productId;
            }
        }

        if (! empty($item['product_barcode'])) {
            $productId = Product::query()->where('barcode', $item['product_barcode'])->value('id');

            if ($productId) {
                return (int) $productId;
            }
        }

        throw new ModelNotFoundException('No fue posible resolver el producto enviado por el POS.');
    }
}
