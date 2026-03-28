<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashClosure\StoreCashClosureRequest;
use App\Http\Requests\CashClosure\UpdateCashClosureStatusRequest;
use App\Models\CashClosure;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CashClosureController extends Controller
{
    public function index(Request $request): View
    {
        $cashClosures = CashClosure::query()
            ->with(['device', 'user.role'])
            ->when($request->filled('device_id'), fn ($query) => $query->where('device_id', $request->integer('device_id')))
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('closed_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('closed_at', '<=', $request->date('date_to')))
            ->latest('closed_at')
            ->paginate(15)
            ->withQueryString();

        return view('cash-closures.index', [
            'cashClosures' => $cashClosures,
            'devices' => Device::query()->orderBy('name')->get(),
            'users' => User::query()->with('role')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('cash-closures.create', [
            'devices' => Device::query()->where('active', true)->orderBy('name')->get(),
            'users' => User::query()->with('role')->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCashClosureRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $cashSales = (float) ($data['cash_sales'] ?? 0);
        $cardSales = (float) ($data['card_sales'] ?? 0);
        $transferSales = (float) ($data['transfer_sales'] ?? 0);
        $openingAmount = (float) ($data['opening_amount'] ?? 0);
        $totalSales = (float) ($data['total_sales'] ?? ($cashSales + $cardSales + $transferSales));
        $expectedAmount = (float) ($data['expected_amount'] ?? ($openingAmount + $cashSales));
        $closingAmount = (float) $data['closing_amount'];
        $difference = (float) ($data['difference'] ?? ($closingAmount - $expectedAmount));

        $cashClosure = CashClosure::query()->create([
            'uuid' => (string) Str::uuid(),
            'device_id' => $data['device_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'opening_amount' => $openingAmount,
            'cash_sales' => $cashSales,
            'card_sales' => $cardSales,
            'transfer_sales' => $transferSales,
            'total_sales' => $totalSales,
            'expected_amount' => $expectedAmount,
            'closing_amount' => $closingAmount,
            'difference' => $difference,
            'status' => $data['status'],
            'source' => $data['source'] ?? CashClosure::SOURCE_SERVER,
            'notes' => $data['notes'] ?? null,
            'opened_at' => $data['opened_at'] ?? null,
            'closed_at' => $data['closed_at'] ?? now(),
            'client_generated_at' => null,
            'received_at' => now(),
        ]);

        return redirect()
            ->route('cash-closures.show', $cashClosure)
            ->with('success', 'Cierre de caja registrado correctamente.');
    }

    public function show(CashClosure $cashClosure): View
    {
        $cashClosure->load(['device', 'user.role']);

        return view('cash-closures.show', compact('cashClosure'));
    }

    public function updateStatus(UpdateCashClosureStatusRequest $request, CashClosure $cashClosure): RedirectResponse
    {
        $cashClosure->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('cash-closures.show', $cashClosure)
            ->with('success', 'Estatus del cierre actualizado correctamente.');
    }
}
