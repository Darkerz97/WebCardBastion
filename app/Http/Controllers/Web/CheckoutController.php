<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CheckoutRequest;
use App\Models\Customer;
use App\Models\Sale;
use App\Services\CartService;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly SaleService $saleService,
    ) {
    }

    public function create(): View|RedirectResponse
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Tu carrito esta vacio.']);
        }

        return view('store.checkout', [
            'cartItems' => $this->cartService->items(),
            'cartSubtotal' => $this->cartService->subtotal(),
            'user' => request()->user(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Tu carrito esta vacio.']);
        }

        $user = $request->user();
        $customer = Customer::query()->firstOrNew(['user_id' => $user->id]);
        $customer->fill([
            'uuid' => $customer->uuid ?: (string) Str::uuid(),
            'name' => $request->validated('contact_name'),
            'email' => $request->validated('contact_email'),
            'phone' => $request->validated('contact_phone'),
            'notes' => $customer->notes,
            'active' => true,
        ]);
        $customer->save();

        try {
            $sale = $this->saleService->create([
                'customer_id' => $customer->id,
                'user_id' => null,
                'sale_number' => null,
                'order_channel' => Sale::CHANNEL_STOREFRONT,
                'contact_name' => $request->validated('contact_name'),
                'contact_email' => $request->validated('contact_email'),
                'contact_phone' => $request->validated('contact_phone'),
                'notes' => $request->validated('notes'),
                'status' => Sale::STATUS_COMPLETED,
                'sold_at' => now(),
                'items' => $this->cartService->items()->map(fn (array $item) => [
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ])->all(),
                'payments' => [[
                    'method' => $request->validated('payment_method'),
                    'amount' => $this->cartService->subtotal(),
                    'reference' => 'WEB-'.now()->format('YmdHis'),
                    'notes' => 'Checkout desde tienda virtual',
                    'paid_at' => now(),
                ]],
            ]);
        } catch (InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['checkout' => $exception->getMessage()]);
        }

        $this->cartService->clear();

        return redirect()->route('account.orders.index')->with('success', "Pedido {$sale->sale_number} creado correctamente.");
    }
}
