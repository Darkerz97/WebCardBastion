<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Product\UpdateStockRequest;
use App\Http\Requests\Sync\SyncIndexRequest;
use App\Http\Resources\ProductResource;
use App\Models\InventoryMovement;
use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryMovementService;
use App\Services\Sync\SyncQueryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly SyncQueryService $syncQueryService,
        private readonly InventoryMovementService $inventoryMovementService,
    ) {
    }

    public function index(SyncIndexRequest $request): JsonResponse
    {
        $query = Product::query()
            ->with('categoryModel')
            ->search($request->string('search')->toString())
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')));

        $result = $this->syncQueryService->resolve($query, $request, [
            'supports_soft_deletes' => true,
            'include_deleted' => false,
            'order_column' => 'updated_at',
            'always_paginate' => true,
            'default_per_page' => 15,
        ]);

        return $this->successResponse(ProductResource::collection($result['records']), 'Productos obtenidos correctamente.', meta: $result['meta']);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($this->payloadFromRequest($request));

        return $this->successResponse(new ProductResource($product), 'Producto creado correctamente.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('categoryModel');

        return $this->successResponse(new ProductResource($product), 'Producto obtenido correctamente.');
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($this->payloadFromRequest($request, $product));

        return $this->successResponse(new ProductResource($product->refresh()), 'Producto actualizado correctamente.');
    }

    public function updateStock(UpdateStockRequest $request, Product $product): JsonResponse
    {
        $this->inventoryMovementService->createManualAdjustment([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'movement_type' => InventoryMovement::TYPE_SYNC_CORRECTION,
            'direction' => InventoryMovement::DIRECTION_ADJUSTMENT,
            'quantity' => $request->integer('stock'),
            'source' => InventoryMovement::SOURCE_SERVER,
            'reference' => 'api.products.updateStock',
            'notes' => 'Correccion de stock registrada desde el endpoint de stock.',
        ]);

        return $this->successResponse(new ProductResource($product->refresh()), 'Stock actualizado correctamente.');
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->successResponse(null, 'Producto eliminado correctamente.');
    }

    private function payloadFromRequest(ProductRequest $request, ?Product $product = null): array
    {
        $data = $request->validated();
        $categoryId = $data['category_id'] ?? null;
        $categoryName = $data['category'] ?? null;

        if ($categoryId && ! $categoryName) {
            $categoryName = Category::query()->find($categoryId)?->name;
        }

        return [
            ...$data,
            'uuid' => $product?->uuid ?? (string) Str::uuid(),
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'category' => $categoryName,
            'featured' => $data['featured'] ?? false,
            'publish_to_store' => $data['publish_to_store'] ?? true,
        ];
    }
}
