<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Preorder\StorePreorderPaymentRequest;
use App\Http\Requests\Preorder\StorePreorderRequest;
use App\Http\Requests\Preorder\UpdatePreorderStatusRequest;
use App\Models\Customer;
use App\Models\Preorder;
use App\Models\Product;
use App\Services\PreorderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class PreorderController extends Controller
{
    public function __construct(private readonly PreorderService $preorderService)
    {
    }

    public function index(Request $request): View
    {
        $preorders = Preorder::query()
            ->with(['customer', 'payments'])
            ->filter($request->only(['customer_id', 'status', 'date_from', 'date_to']))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('preorders.index', [
            'preorders' => $preorders,
            'customers' => Customer::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('preorders.create', [
            'customers' => Customer::query()->where('active', true)->orderBy('name')->get(),
            'products' => Product::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StorePreorderRequest $request): RedirectResponse
    {
        try {
            $preorder = $this->preorderService->create($request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['items' => $exception->getMessage()]);
        }

        return redirect()
            ->route('preorders.show', $preorder)
            ->with('success', 'Preventa creada correctamente.');
    }

    public function show(Preorder $preorder): View
    {
        $preorder->load(['customer.user.role', 'items.product', 'payments']);

        return view('preorders.show', compact('preorder'));
    }

    public function addPayment(StorePreorderPaymentRequest $request, Preorder $preorder): RedirectResponse
    {
        try {
            $this->preorderService->registerPayment($preorder, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['payment' => $exception->getMessage()]);
        }

        return redirect()
            ->route('preorders.show', $preorder)
            ->with('success', 'Abono registrado correctamente.');
    }

    public function updateStatus(UpdatePreorderStatusRequest $request, Preorder $preorder): RedirectResponse
    {
        $status = $request->validated('status');

        if ($status === Preorder::STATUS_DELIVERED && (float) $preorder->amount_due > 0) {
            return back()->withErrors(['status' => 'No puedes marcar como entregada una preventa con saldo pendiente.']);
        }

        if ($status === Preorder::STATUS_CANCELLED && (float) $preorder->amount_paid > 0) {
            return back()->withErrors(['status' => 'No puedes cancelar una preventa que ya tiene abonos registrados.']);
        }

        $preorder->update(['status' => $status]);
        $this->preorderService->refreshBalances($preorder);

        return redirect()
            ->route('preorders.show', $preorder)
            ->with('success', 'Estatus de preventa actualizado correctamente.');
    }
}
