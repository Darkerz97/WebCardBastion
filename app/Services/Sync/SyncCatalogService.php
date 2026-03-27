<?php

namespace App\Services\Sync;

use App\Http\Requests\Sync\SyncCatalogRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\PosSiteSettingResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Support\Carbon;

class SyncCatalogService
{
    public const DEFAULT_INCLUDES = ['products', 'categories', 'customers'];

    public function build(SyncCatalogRequest $request): array
    {
        $includes = $this->resolveIncludes($request->validated('include', []));
        $updatedSince = $request->input('updated_since');
        $metaCounts = [];
        $data = [];

        if (in_array('products', $includes, true)) {
            $products = Product::query()
                ->with(['categoryModel', 'images'])
                ->when($updatedSince, fn ($query) => $query->withTrashed()->where('updated_at', '>=', Carbon::parse((string) $updatedSince)), fn ($query) => $query->withTrashed())
                ->orderBy('updated_at')
                ->orderBy('id')
                ->get();

            $data['products'] = ProductResource::collection($products)->resolve();
            $metaCounts['products'] = $products->count();
        }

        if (in_array('categories', $includes, true)) {
            $categories = Category::query()
                ->withCount('products')
                ->when($updatedSince, fn ($query) => $query->where('updated_at', '>=', Carbon::parse((string) $updatedSince)))
                ->orderBy('updated_at')
                ->orderBy('id')
                ->get();

            $data['categories'] = CategoryResource::collection($categories)->resolve();
            $metaCounts['categories'] = $categories->count();
        }

        if (in_array('customers', $includes, true)) {
            $customers = Customer::query()
                ->withTrashed()
                ->when($updatedSince, fn ($query) => $query->where('updated_at', '>=', Carbon::parse((string) $updatedSince)))
                ->orderBy('updated_at')
                ->orderBy('id')
                ->get();

            $data['customers'] = CustomerResource::collection($customers)->resolve();
            $metaCounts['customers'] = $customers->count();
        }

        if (in_array('settings', $includes, true)) {
            $settings = SiteSetting::query()
                ->when($updatedSince, fn ($query) => $query->where('updated_at', '>=', Carbon::parse((string) $updatedSince)))
                ->orderBy('updated_at')
                ->orderBy('id')
                ->get();

            $data['settings'] = PosSiteSettingResource::collection($settings)->resolve();
            $metaCounts['settings'] = $settings->count();
        }

        return [
            'data' => $data,
            'meta' => [
                'server_time' => now()->toIso8601String(),
                'requested_updated_since' => $updatedSince,
                'includes' => $includes,
                'counts' => $metaCounts,
                'next_recommended_sync_at' => now()->addMinutes(5)->toIso8601String(),
            ],
        ];
    }

    private function resolveIncludes(array $requestedIncludes): array
    {
        return $requestedIncludes === [] ? self::DEFAULT_INCLUDES : array_values(array_unique($requestedIncludes));
    }
}
