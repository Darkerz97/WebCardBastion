<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        return view('store.home');
    }

    public function catalog(Request $request): View
    {
        $selectedCategory = $request->string('category')->toString();
        $products = Product::query()
            ->with(['categoryModel', 'images'])
            ->published()
            ->search($request->string('search')->toString())
            ->when($selectedCategory, fn ($query) => $query->whereHas('categoryModel', fn ($categoryQuery) => $categoryQuery->where('slug', $selectedCategory)))
            ->orderByDesc('featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->active()
            ->withCount([
                'products' => fn ($query) => $query->published(),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('store.index', [
            'products' => $products,
            'categories' => $categories,
            'featuredProducts' => Product::query()->with(['categoryModel', 'images'])->published()->where('featured', true)->limit(4)->get(),
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->active && $product->publish_to_store, 404);

        $product->load(['categoryModel', 'images']);

        return view('store.show', [
            'product' => $product,
            'relatedProducts' => Product::query()
                ->with(['categoryModel', 'images'])
                ->published()
                ->where('id', '!=', $product->id)
                ->when($product->category_id, fn ($query) => $query->where('category_id', $product->category_id))
                ->orderByDesc('featured')
                ->limit(4)
                ->get(),
        ]);
    }
}
