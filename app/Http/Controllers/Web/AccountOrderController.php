<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\View\View;

class AccountOrderController extends Controller
{
    public function index(): View
    {
        $user = request()->user();
        $customerId = Customer::query()->where('user_id', $user->id)->value('id');

        $orders = Sale::query()
            ->with(['items.product', 'payments'])
            ->where('order_channel', Sale::CHANNEL_STOREFRONT)
            ->when($customerId, fn ($query) => $query->where('customer_id', $customerId), fn ($query) => $query->whereRaw('1 = 0'))
            ->latest('sold_at')
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }
}
