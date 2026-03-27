<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\TournamentRegistration;
use Illuminate\Support\Facades\Schema;
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
            'recentOrders' => $customer
                ? Sale::query()
                    ->where('customer_id', $customer->id)
                    ->where('order_channel', Sale::CHANNEL_STOREFRONT)
                    ->latest('sold_at')
                    ->limit(5)
                    ->get()
                : collect(),
            'recentTournaments' => TournamentRegistration::query()
                ->with('tournament')
                ->where('user_id', $user->id)
                ->latest()
                ->limit(4)
                ->get(),
            'latestArticles' => Schema::hasTable('articles')
                ? Article::query()->published()->latest('published_at')->limit(3)->get()
                : collect(),
            'stats' => [
                'orders' => $customer
                    ? Sale::query()->where('customer_id', $customer->id)->where('order_channel', Sale::CHANNEL_STOREFRONT)->count()
                    : 0,
                'credits' => (float) ($customer?->credit_balance ?? 0),
                'tournaments' => TournamentRegistration::query()->where('user_id', $user->id)->count(),
                'win_rate' => $this->calculateWinRate($user->id),
            ],
        ]);
    }

    private function calculateWinRate(int $userId): string
    {
        $registration = TournamentRegistration::query()
            ->where('user_id', $userId)
            ->selectRaw('SUM(wins) as wins, SUM(losses) as losses, SUM(draws) as draws')
            ->first();

        $wins = (int) ($registration?->wins ?? 0);
        $losses = (int) ($registration?->losses ?? 0);
        $draws = (int) ($registration?->draws ?? 0);
        $total = $wins + $losses + $draws;

        if ($total === 0) {
            return '0%';
        }

        return number_format(($wins / $total) * 100, 1).'%';
    }
}
