<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $categories = Category::query()
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')))
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $this->successResponse(CategoryResource::collection($categories), 'Categorias obtenidas correctamente.');
    }

    public function show(Category $category): JsonResponse
    {
        $category->loadCount('products');

        return $this->successResponse(new CategoryResource($category), 'Categoria obtenida correctamente.');
    }
}
