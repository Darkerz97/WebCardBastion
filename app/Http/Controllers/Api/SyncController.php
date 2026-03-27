<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sync\HeartbeatRequest;
use App\Http\Requests\Sync\SyncIndexRequest;
use App\Http\Requests\Sync\UploadSalesRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\DeviceResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Customer;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Sync\SyncAuthorityService;
use App\Services\Sync\SyncHeartbeatService;
use App\Services\Sync\SyncQueryService;
use App\Services\Sync\SyncSaleUploadService;

class SyncController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SyncAuthorityService $syncAuthorityService,
        private readonly SyncHeartbeatService $syncHeartbeatService,
        private readonly SyncQueryService $syncQueryService,
        private readonly SyncSaleUploadService $syncSaleUploadService,
    ) {
    }

    public function products(SyncIndexRequest $request): JsonResponse
    {
        $result = $this->syncQueryService->resolve(Product::query(), $request, [
            'supports_soft_deletes' => true,
            'prefer_cursor' => true,
            'include_deleted' => true,
            'default_per_page' => 100,
        ]);

        return $this->successResponse(
            ProductResource::collection($result['records']),
            'Productos listos para sincronizacion.',
            meta: [
                ...$this->syncAuthorityService->forCatalog(),
                ...$result['meta'],
            ],
        );
    }

    public function customers(SyncIndexRequest $request): JsonResponse
    {
        $result = $this->syncQueryService->resolve(Customer::query(), $request, [
            'supports_soft_deletes' => true,
            'prefer_cursor' => true,
            'include_deleted' => true,
            'default_per_page' => 100,
        ]);

        return $this->successResponse(
            CustomerResource::collection($result['records']),
            'Clientes listos para sincronizacion.',
            meta: [
                ...$this->syncAuthorityService->forCatalog(),
                ...$result['meta'],
            ],
        );
    }

    public function heartbeat(HeartbeatRequest $request): JsonResponse
    {
        $device = $this->syncHeartbeatService->register($request->validated());

        return $this->successResponse(new DeviceResource($device), 'Heartbeat registrado correctamente.');
    }

    public function uploadSales(UploadSalesRequest $request): JsonResponse
    {
        $device = $this->syncSaleUploadService->resolveDevice($request->validated('device_code'));
        $results = $this->syncSaleUploadService->upload($device, $request->validated('sales'));

        return $this->successResponse($results, 'Proceso de sincronizacion completado.', meta: $this->syncAuthorityService->forSalesUpload());
    }
}
