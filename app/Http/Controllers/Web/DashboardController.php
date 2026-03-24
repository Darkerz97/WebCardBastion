<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
                'products' => Product::query()->count(),
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
        ]);
    }
}
