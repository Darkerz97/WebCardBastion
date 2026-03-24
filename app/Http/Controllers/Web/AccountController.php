<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $customer = Customer::query()
            ->withCount('sales')
            ->where(function ($query) use ($user): void {
                $query->where('email', $user->email);

                if ($user->phone) {
                    $query->orWhere('phone', $user->phone);
                }
            })
            ->first();

        return view('account.dashboard', [
            'user' => $user,
            'customerProfile' => $customer,
            'stats' => [
                'orders' => $customer?->sales_count ?? 0,
                'credits' => (float) ($customer?->credit_balance ?? 0),
                'tournaments' => 0,
                'win_rate' => 'Proximamente',
            ],
        ]);
    }
}
