<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sync\UploadCashClosuresRequest;
use App\Services\Sync\SyncAuthorityService;
use App\Services\Sync\SyncBatchResultService;
use App\Services\Sync\SyncCashClosureUploadService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SyncCashClosureController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SyncCashClosureUploadService $syncCashClosureUploadService,
        private readonly SyncAuthorityService $syncAuthorityService,
        private readonly SyncBatchResultService $syncBatchResultService,
    ) {
    }

    public function upload(UploadCashClosuresRequest $request): JsonResponse
    {
        $device = $this->syncCashClosureUploadService->resolveDevice($request->validated('device_code'));
        $results = $this->syncCashClosureUploadService->upload($device, $request->validated('closures'));

        return $this->successResponse($results, 'Proceso de sincronizacion de cierres completado.', meta: [
            ...$this->syncAuthorityService->forCashClosuresUpload(),
            'summary' => $this->syncBatchResultService->summarize($results),
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
