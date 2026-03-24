<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use App\Support\CsvReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $saleService)
    {
    }

    public function index(Request $request): View
    {
        $sales = Sale::query()
            ->with(['customer', 'user', 'device'])
            ->filter($request->only(['customer_id', 'user_id', 'device_id', 'status', 'date_from', 'date_to']))
            ->latest('sold_at')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'customers' => Customer::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('sales.create', [
            'customers' => Customer::query()->where('active', true)->orderBy('name')->get(),
            'devices' => Device::query()->where('active', true)->orderBy('name')->get(),
            'products' => Product::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function template(): StreamedResponse
    {
        $headers = [
            'sale_number',
            'customer_email',
            'customer_phone',
            'device_code',
            'sold_at',
            'status',
            'discount',
            'product_sku',
            'quantity',
            'unit_price',
            'payment_method',
            'payment_amount',
            'payment_reference',
            'payment_notes',
            'payment_paid_at',
        ];

        $rows = [
            ['SALE-IMPORT-001', 'juan@example.com', '5512345678', '', '2026-03-24 10:00:00', 'completed', '0', 'MIC-001', '2', '89.00', 'cash', '178.00', '', '', '2026-03-24 10:00:00'],
            ['SALE-IMPORT-001', 'juan@example.com', '5512345678', '', '2026-03-24 10:00:00', 'completed', '0', 'FUN-002', '1', '199.00', '', '', '', '', ''],
        ];

        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, 'plantilla_ventas.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        try {
            $rows = CsvReader::read($request->file('file'));

            if ($rows === []) {
                throw new InvalidArgumentException('La plantilla de ventas no contiene filas para importar.');
            }

            $groupedSales = collect($rows)->groupBy('sale_number');
            $processed = 0;

            DB::transaction(function () use ($groupedSales, $request, &$processed): void {
                foreach ($groupedSales as $saleNumber => $saleRows) {
                    $firstRow = $saleRows->first();
                    $line = $firstRow['_row'];

                    if (! $saleNumber) {
                        throw new InvalidArgumentException("Fila {$line}: sale_number es obligatorio para agrupar la venta.");
                    }

                    if (Sale::query()->where('sale_number', $saleNumber)->exists()) {
                        throw new InvalidArgumentException("Fila {$line}: la venta {$saleNumber} ya existe.");
                    }

                    $headerValidator = Validator::make($firstRow, [
                        'customer_email' => ['nullable', 'email', 'max:255'],
                        'customer_phone' => ['nullable', 'string', 'max:50'],
                        'device_code' => ['nullable', 'string', 'max:100'],
                        'sold_at' => ['nullable', 'date'],
                        'status' => ['nullable', 'in:draft,completed,cancelled'],
                        'discount' => ['nullable', 'numeric', 'min:0'],
                    ]);

                    if ($headerValidator->fails()) {
                        throw new InvalidArgumentException("Fila {$line}: ".$headerValidator->errors()->first());
                    }

                    $headerData = $headerValidator->validated();
                    $customer = $this->resolveCustomer(
                        $headerData['customer_email'] ?? null,
                        $headerData['customer_phone'] ?? null,
                        $line,
                    );
                    $device = $this->resolveDevice($headerData['device_code'] ?? null, $line);
                    $items = [];
                    $payments = [];

                    foreach ($saleRows as $row) {
                        $itemLine = $row['_row'];
                        $itemValidator = Validator::make($row, [
                            'product_sku' => ['required', 'string', 'max:100'],
                            'quantity' => ['required', 'integer', 'min:1'],
                            'unit_price' => ['nullable', 'numeric', 'min:0'],
                            'payment_method' => ['nullable', 'in:cash,card,transfer,credit,mixed'],
                            'payment_amount' => ['nullable', 'numeric', 'min:0.01'],
                            'payment_reference' => ['nullable', 'string', 'max:255'],
                            'payment_notes' => ['nullable', 'string'],
                            'payment_paid_at' => ['nullable', 'date'],
                        ]);

                        if ($itemValidator->fails()) {
                            throw new InvalidArgumentException("Fila {$itemLine}: ".$itemValidator->errors()->first());
                        }

                        $itemData = $itemValidator->validated();
                        $product = Product::query()->where('sku', $itemData['product_sku'])->first();

                        if (! $product) {
                            throw new InvalidArgumentException("Fila {$itemLine}: no existe un producto con SKU {$itemData['product_sku']}.");
                        }

                        $items[] = [
                            'product_id' => $product->id,
                            'quantity' => (int) $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'] ?? null,
                        ];

                        if (($itemData['payment_method'] ?? null) || ($itemData['payment_amount'] ?? null)) {
                            if (empty($itemData['payment_method']) || empty($itemData['payment_amount'])) {
                                throw new InvalidArgumentException("Fila {$itemLine}: payment_method y payment_amount deben capturarse juntos.");
                            }

                            $signature = implode('|', [
                                $itemData['payment_method'],
                                $itemData['payment_amount'],
                                $itemData['payment_reference'] ?? '',
                                $itemData['payment_notes'] ?? '',
                                $itemData['payment_paid_at'] ?? '',
                            ]);

                            $payments[$signature] = [
                                'method' => $itemData['payment_method'],
                                'amount' => $itemData['payment_amount'],
                                'reference' => $itemData['payment_reference'] ?? null,
                                'notes' => $itemData['payment_notes'] ?? null,
                                'paid_at' => $itemData['payment_paid_at'] ?? null,
                            ];
                        }
                    }

                    $this->saleService->create([
                        'sale_number' => $saleNumber,
                        'customer_id' => $customer?->id,
                        'user_id' => $request->user()->id,
                        'device_id' => $device?->id,
                        'status' => $headerData['status'] ?? Sale::STATUS_COMPLETED,
                        'discount' => $headerData['discount'] ?? 0,
                        'sold_at' => $headerData['sold_at'] ?? now(),
                        'items' => $items,
                        'payments' => array_values($payments),
                    ]);

                    $processed++;
                }
            });

            return redirect()->route('sales.index')->with('success', "Importacion masiva completada: {$processed} ventas procesadas.");
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['file' => $exception->getMessage()]);
        }
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $this->saleService->create([
                ...$request->validated(),
                'user_id' => $request->user()->id,
            ]);
        } catch (InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['items' => $exception->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Venta registrada correctamente.');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'user.role', 'device', 'items.product', 'payments']);

        return view('sales.show', compact('sale'));
    }

    private function resolveCustomer(?string $email, ?string $phone, int $line): ?Customer
    {
        if (! $email && ! $phone) {
            return null;
        }

        $emailCustomer = $email ? Customer::query()->where('email', $email)->first() : null;
        $phoneCustomer = $phone ? Customer::query()->where('phone', $phone)->first() : null;

        if ($emailCustomer && $phoneCustomer && $emailCustomer->id !== $phoneCustomer->id) {
            throw new InvalidArgumentException("Fila {$line}: customer_email y customer_phone apuntan a clientes distintos.");
        }

        $customer = $emailCustomer ?? $phoneCustomer;

        if (! $customer) {
            throw new InvalidArgumentException("Fila {$line}: el cliente indicado no existe.");
        }

        return $customer;
    }

    private function resolveDevice(?string $deviceCode, int $line): ?Device
    {
        if (! $deviceCode) {
            return null;
        }

        $device = Device::query()->where('device_code', $deviceCode)->first();

        if (! $device) {
            throw new InvalidArgumentException("Fila {$line}: no existe un dispositivo con codigo {$deviceCode}.");
        }

        return $device;
    }
}
