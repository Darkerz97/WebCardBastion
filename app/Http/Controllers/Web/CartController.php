<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\AddToCartRequest;
use App\Http\Requests\Store\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index(): View
    {
        return view('store.cart', [
            'cartItems' => $this->cartService->items(),
            'cartSubtotal' => $this->cartService->subtotal(),
        ]);
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $product = Product::query()->published()->findOrFail($request->integer('product_id'));
        $this->cartService->add($product, $request->integer('quantity'));

        return redirect()->route('cart.index')->with('success', 'Producto agregado al carrito.');
    }

    public function update(UpdateCartItemRequest $request, Product $product): RedirectResponse
    {
        $this->cartService->update($product, $request->integer('quantity'));

        return back()->with('success', 'Carrito actualizado.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->cartService->remove($product);

        return back()->with('success', 'Producto eliminado del carrito.');
    }
}
