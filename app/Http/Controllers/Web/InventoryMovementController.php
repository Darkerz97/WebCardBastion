<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreAdminInventoryMovementRequest;
use App\Models\Device;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\InventoryMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use InvalidArgumentException;

class InventoryMovementController extends Controller
{
    public function __construct(private readonly InventoryMovementService $inventoryMovementService)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'product_id' => $request->input('product_id'),
            'device_id' => $request->input('device_id'),
            'movement_type' => $request->input('movement_type'),
            'source' => $request->input('source'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $baseQuery = InventoryMovement::query()
            ->with(['product', 'sale', 'device', 'user.role'])
            ->filter($filters)
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->when($request->filled('direction'), fn ($query) => $query->where('direction', $request->string('direction')->toString()))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->toString();

                $query->where(function ($builder) use ($term): void {
                    $builder
                        ->where('uuid', 'like', "%{$term}%")
                        ->orWhere('reference', 'like', "%{$term}%")
                        ->orWhere('notes', 'like', "%{$term}%")
                        ->orWhereHas('product', fn ($productQuery) => $productQuery
                            ->where('name', 'like', "%{$term}%")
                            ->orWhere('sku', 'like', "%{$term}%")
                            ->orWhere('barcode', 'like', "%{$term}%"))
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('device', fn ($deviceQuery) => $deviceQuery
                            ->where('name', 'like', "%{$term}%")
                            ->orWhere('device_code', 'like', "%{$term}%"));
                });
            });

        $movements = (clone $baseQuery)
            ->latest('occurred_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $summaryQuery = (clone $baseQuery)->toBase();
        $summary = [
            'movements' => (clone $summaryQuery)->count(),
            'products' => (clone $summaryQuery)->distinct('product_id')->count('product_id'),
            'entries' => (clone $summaryQuery)->where('direction', InventoryMovement::DIRECTION_IN)->sum('quantity'),
            'exits' => (clone $summaryQuery)->where('direction', InventoryMovement::DIRECTION_OUT)->sum('quantity'),
            'adjustments' => (clone $summaryQuery)->where('direction', InventoryMovement::DIRECTION_ADJUSTMENT)->count(),
        ];

        $auditProducts = Product::query()
            ->withCount('inventoryMovements')
            ->where(function ($query): void {
                $query
                    ->whereColumn('stock', '<=', 'min_stock')
                    ->orWhereHas('inventoryMovements', fn ($movementQuery) => $movementQuery->where('occurred_at', '>=', now()->subDays(7)));
            })
            ->orderByRaw('stock <= min_stock desc')
            ->orderByDesc('inventory_movements_count')
            ->orderBy('name')
            ->limit(6)
            ->get();

        return view('inventory-movements.index', [
            'movements' => $movements,
            'summary' => $summary,
            'auditProducts' => $auditProducts,
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'sku', 'stock', 'min_stock']),
            'devices' => Device::query()->orderBy('name')->get(['id', 'name', 'device_code']),
            'users' => User::query()->with('role')->orderBy('name')->get(['id', 'name', 'role_id']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('inventory-movements.create', [
            'selectedProductId' => $request->integer('product_id') ?: null,
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'sku', 'stock']),
            'devices' => Device::query()->where('active', true)->orderBy('name')->get(),
            'users' => User::query()->with('role')->where('active', true)->orderBy('name')->get(),
            'sales' => Sale::query()->latest('sold_at')->limit(50)->get(['id', 'sale_number']),
        ]);
    }

    public function store(StoreAdminInventoryMovementRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $movement = $this->inventoryMovementService->createManualAdjustment([
                ...$data,
                'user_id' => $data['user_id'] ?? $request->user()?->id,
                'source' => $data['source'] ?? InventoryMovement::SOURCE_SERVER,
                'occurred_at' => ! empty($data['occurred_at'])
                    ? Carbon::parse($data['occurred_at'])
                    : now(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => $exception->getMessage()]);
        }

        return redirect()
            ->route('inventory-movements.show', $movement)
            ->with('success', 'Movimiento de inventario registrado correctamente.');
    }

    public function show(InventoryMovement $inventoryMovement): View
    {
        $inventoryMovement->load(['product', 'sale', 'device', 'user.role']);

        $relatedMovements = InventoryMovement::query()
            ->with(['sale', 'device', 'user.role'])
            ->where('product_id', $inventoryMovement->product_id)
            ->whereKeyNot($inventoryMovement->id)
            ->latest('occurred_at')
            ->limit(8)
            ->get();

        return view('inventory-movements.show', [
            'inventoryMovement' => $inventoryMovement,
            'relatedMovements' => $relatedMovements,
        ]);
    }
}
