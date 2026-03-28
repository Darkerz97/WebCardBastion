<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Preorder\StorePreorderPaymentRequest;
use App\Http\Requests\Preorder\StorePreorderRequest;
use App\Http\Requests\Sync\SyncIndexRequest;
use App\Http\Resources\PreorderPaymentResource;
use App\Http\Resources\PreorderResource;
use App\Models\Preorder;
use App\Services\PreorderService;
use App\Services\Sync\SyncQueryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class PreorderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly PreorderService $preorderService,
        private readonly SyncQueryService $syncQueryService,
    ) {
    }

    public function index(SyncIndexRequest $request): JsonResponse
    {
        $query = Preorder::query()
            ->with(['customer', 'items.product', 'payments'])
            ->filter($request->only(['customer_id', 'status', 'date_from', 'date_to']));

        $result = $this->syncQueryService->resolve($query, $request, [
            'supports_soft_deletes' => false,
            'order_column' => 'updated_at',
            'always_paginate' => true,
            'default_per_page' => 15,
        ]);

        return $this->successResponse(PreorderResource::collection($result['records']), 'Preventas obtenidas correctamente.', meta: $result['meta']);
    }

    public function store(StorePreorderRequest $request): JsonResponse
    {
        try {
            $preorder = $this->preorderService->create($request->validated());
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new PreorderResource($preorder), 'Preventa creada correctamente.', 201);
    }

    public function show(Preorder $preorder): JsonResponse
    {
        $preorder->load(['customer', 'items.product', 'payments']);

        return $this->successResponse(new PreorderResource($preorder), 'Preventa obtenida correctamente.');
    }

    public function addPayment(StorePreorderPaymentRequest $request, Preorder $preorder): JsonResponse
    {
        try {
            $payment = $this->preorderService->registerPayment($preorder, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new PreorderPaymentResource($payment), 'Abono registrado correctamente.', 201);
    }
}
