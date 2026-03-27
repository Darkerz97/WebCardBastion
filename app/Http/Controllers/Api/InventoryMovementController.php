<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\InventoryMovementIndexRequest;
use App\Http\Requests\Inventory\StoreInventoryMovementRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\InventoryMovement;
use App\Services\InventoryMovementService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class InventoryMovementController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly InventoryMovementService $inventoryMovementService)
    {
    }

    public function index(InventoryMovementIndexRequest $request): JsonResponse
    {
        $movements = InventoryMovement::query()
            ->with(['product', 'sale', 'device', 'user.role'])
            ->filter($request->validated())
            ->latest('occurred_at')
            ->latest('id')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse(InventoryMovementResource::collection($movements), 'Movimientos de inventario obtenidos correctamente.');
    }

    public function store(StoreInventoryMovementRequest $request): JsonResponse
    {
        $movement = $this->inventoryMovementService->createManualAdjustment($request->validated());

        return $this->successResponse(new InventoryMovementResource($movement), 'Movimiento de inventario registrado correctamente.', 201);
    }
}
