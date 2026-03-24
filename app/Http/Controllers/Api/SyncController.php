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
use App\Services\SaleService;
use App\Services\SyncLogService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;

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

        return $this->successResponse(ProductResource::collection($products), 'Productos listos para sincronización.');
    }

    public function customers(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->when($request->filled('updated_since'), fn ($query) => $query->where('updated_at', '>=', $request->date('updated_since')))
            ->orderBy('updated_at')
            ->get();

        return $this->successResponse(CustomerResource::collection($customers), 'Clientes listos para sincronización.');
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
        $device = Device::query()->where('device_code', $request->string('device_code'))->first();
        $results = [];

        foreach ($request->validated('sales') as $payload) {
            $existingSale = Sale::query()->where('uuid', $payload['uuid'])->first();

            if ($existingSale) {
                $results[] = [
                    'uuid' => $existingSale->uuid,
                    'status' => 'skipped',
                    'message' => 'La venta ya había sido sincronizada.',
                ];

                $this->syncLogService->log($device, 'sale', $existingSale->uuid, 'upload', 'skipped', $payload, 'Venta duplicada omitida.', now());

                continue;
            }

            try {
                $sale = $this->saleService->create([
                    ...$payload,
                    'device_id' => $device?->id,
                ]);

                $results[] = [
                    'uuid' => $sale->uuid,
                    'status' => 'created',
                    'sale' => new SaleResource($sale),
                ];

                $this->syncLogService->log($device, 'sale', $sale->uuid, 'upload', 'success', $payload, 'Venta sincronizada correctamente.', now());
            } catch (InvalidArgumentException $exception) {
                $results[] = [
                    'uuid' => $payload['uuid'],
                    'status' => 'failed',
                    'message' => $exception->getMessage(),
                ];

                $this->syncLogService->log($device, 'sale', $payload['uuid'], 'upload', 'failed', $payload, $exception->getMessage(), now());
            }
        }

        return $this->successResponse($results, 'Proceso de sincronización completado.');
    }
}
