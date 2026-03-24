<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\SaleService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class SaleController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SaleService $saleService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $sales = Sale::query()
            ->with(['customer', 'user.role', 'device'])
            ->filter($request->only(['customer_id', 'user_id', 'device_id', 'status', 'date_from', 'date_to']))
            ->latest('sold_at')
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse(SaleResource::collection($sales), 'Ventas obtenidas correctamente.', meta: [
            'current_page' => $sales->currentPage(),
            'last_page' => $sales->lastPage(),
            'per_page' => $sales->perPage(),
            'total' => $sales->total(),
        ]);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->saleService->create($request->validated());
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new SaleResource($sale), 'Venta creada correctamente.', 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['customer', 'user.role', 'device', 'items.product', 'payments']);

        return $this->successResponse(new SaleResource($sale), 'Venta obtenida correctamente.');
    }

    public function addPayment(StorePaymentRequest $request, Sale $sale): JsonResponse
    {
        try {
            $payment = $this->saleService->registerPayment($sale, $request->validated());
        } catch (InvalidArgumentException $exception) {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new PaymentResource($payment), 'Pago registrado correctamente.', 201);
    }
}
