<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Product\UpdateStockRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->search($request->string('search')->toString())
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')))
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse(ProductResource::collection($products), 'Productos obtenidos correctamente.', meta: [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create([
            ...$request->validated(),
            'uuid' => (string) Str::uuid(),
        ]);

        return $this->successResponse(new ProductResource($product), 'Producto creado correctamente.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->successResponse(new ProductResource($product), 'Producto obtenido correctamente.');
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return $this->successResponse(new ProductResource($product->refresh()), 'Producto actualizado correctamente.');
    }

    public function updateStock(UpdateStockRequest $request, Product $product): JsonResponse
    {
        $product->update(['stock' => $request->integer('stock')]);

        return $this->successResponse(new ProductResource($product->refresh()), 'Stock actualizado correctamente.');
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->successResponse(null, 'Producto eliminado correctamente.');
    }
}
