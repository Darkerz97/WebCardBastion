<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $todaySales = Sale::query()->whereDate('sold_at', today());

        return view('dashboard.index', [
            'metrics' => [
                'categories' => Category::query()->count(),
                'products' => Product::query()->count(),
                'published_products' => Product::query()->published()->count(),
                'customers' => Customer::query()->count(),
                'sales' => Sale::query()->count(),
                'sales_today' => (clone $todaySales)->count(),
                'amount_today' => (float) (clone $todaySales)->sum('total'),
            ],
            'recentSales' => Sale::query()
                ->with(['customer', 'user'])
                ->latest('sold_at')
                ->limit(8)
                ->get(),
            'lowStockProducts' => Product::query()
                ->whereColumn('stock', '<=', 'min_stock')
                ->where('active', true)
                ->orderBy('stock')
                ->limit(6)
                ->get(),
        ]);
    }
}
