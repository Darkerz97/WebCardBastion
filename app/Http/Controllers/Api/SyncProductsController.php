<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sync\UploadProductsRequest;
use App\Services\Sync\SyncProductUploadService;
use Illuminate\Http\JsonResponse;

class SyncProductsController extends Controller
{
    public function __construct(
        private readonly SyncProductUploadService $syncProductUploadService,
    ) {
    }

    public function upload(UploadProductsRequest $request): JsonResponse
    {
        $results = $this->syncProductUploadService->upload($request->validated('products'));

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }
}
