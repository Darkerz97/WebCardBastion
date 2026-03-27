<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sync\UploadInventoryMovementsRequest;
use App\Services\InventoryMovementService;
use App\Services\Sync\SyncAuthorityService;
use App\Services\Sync\SyncBatchResultService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SyncInventoryMovementController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly InventoryMovementService $inventoryMovementService,
        private readonly SyncAuthorityService $syncAuthorityService,
        private readonly SyncBatchResultService $syncBatchResultService,
    ) {
    }

    public function upload(UploadInventoryMovementsRequest $request): JsonResponse
    {
        $device = $this->inventoryMovementService->resolveDeviceByCode($request->validated('device_code'));
        $results = $this->inventoryMovementService->uploadFromPos($device, $request->validated('movements'));

        return $this->successResponse($results, 'Proceso de sincronizacion de movimientos completado.', meta: [
            ...$this->syncAuthorityService->forInventoryMovementsUpload(),
            'summary' => $this->syncBatchResultService->summarize($results),
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
