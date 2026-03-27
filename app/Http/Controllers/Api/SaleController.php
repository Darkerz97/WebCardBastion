<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sync\SyncIndexRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\Sync\SyncQueryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SaleController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SaleService $saleService,
        private readonly SyncQueryService $syncQueryService,
    ) {
    }

    public function index(SyncIndexRequest $request): JsonResponse
    {
        $query = Sale::query()
            ->with(['customer', 'user.role', 'device'])
            ->filter($request->only(['customer_id', 'user_id', 'device_id', 'status', 'date_from', 'date_to']));

        $result = $this->syncQueryService->resolve($query, $request, [
            'supports_soft_deletes' => false,
            'order_column' => 'updated_at',
            'always_paginate' => true,
            'default_per_page' => 15,
        ]);

        return $this->successResponse(SaleResource::collection($result['records']), 'Ventas obtenidas correctamente.', meta: $result['meta']);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->saleService->create($request->validated());
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new SaleResource($sale), 'Venta creada correctamente.', 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['customer', 'user.role', 'device', 'items.product', 'payments']);

        return $this->successResponse(new SaleResource($sale), 'Venta obtenida correctamente.');
    }

    public function addPayment(StorePaymentRequest $request, Sale $sale): JsonResponse
    {
        try {
            $payment = $this->saleService->registerPayment($sale, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new PaymentResource($payment), 'Pago registrado correctamente.', 201);
    }
}
