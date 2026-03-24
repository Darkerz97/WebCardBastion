<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use App\Support\CsvReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->search($request->string('search')->toString())
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create', ['product' => new Product()]);
    }

    public function template(): StreamedResponse
    {
        $headers = ['name', 'sku', 'barcode', 'description', 'category', 'cost', 'price', 'stock', 'image_path', 'active'];
        $rows = [
            ['Mica templada', 'MIC-001', '750100000001', 'Proteccion frontal premium', 'Accesorios', '35.00', '89.00', '25', '', '1'],
        ];

        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, 'plantilla_productos.csv', [
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
                throw new InvalidArgumentException('La plantilla de productos no contiene filas para importar.');
            }

            $imported = 0;

            DB::transaction(function () use ($rows, &$imported): void {
                foreach ($rows as $row) {
                    $line = $row['_row'];

                    $validator = Validator::make($row, [
                        'name' => ['required', 'string', 'max:255'],
                        'sku' => ['required', 'string', 'max:100'],
                        'barcode' => ['nullable', 'string', 'max:100'],
                        'description' => ['nullable', 'string'],
                        'category' => ['nullable', 'string', 'max:100'],
                        'cost' => ['required', 'numeric', 'min:0'],
                        'price' => ['required', 'numeric', 'min:0'],
                        'stock' => ['required', 'integer', 'min:0'],
                        'image_path' => ['nullable', 'string', 'max:255'],
                        'active' => ['nullable'],
                    ]);

                    if ($validator->fails()) {
                        throw new InvalidArgumentException("Fila {$line}: ".$validator->errors()->first());
                    }

                    $data = $validator->validated();
                    $active = $this->parseBoolean($data['active'] ?? null, true, $line);
                    $product = Product::withTrashed()->where('sku', $data['sku'])->first();

                    if (! empty($data['barcode'])) {
                        $barcodeOwner = Product::withTrashed()
                            ->where('barcode', $data['barcode'])
                            ->when($product, fn ($query) => $query->where('id', '!=', $product->id))
                            ->first();

                        if ($barcodeOwner) {
                            throw new InvalidArgumentException("Fila {$line}: el barcode {$data['barcode']} ya pertenece a otro producto.");
                        }
                    }

                    if ($product) {
                        if ($product->trashed()) {
                            $product->restore();
                        }

                        $product->update([
                            ...$data,
                            'barcode' => $data['barcode'] ?: null,
                            'description' => $data['description'] ?: null,
                            'category' => $data['category'] ?: null,
                            'image_path' => $data['image_path'] ?: null,
                            'active' => $active,
                        ]);
                    } else {
                        Product::create([
                            ...$data,
                            'uuid' => (string) Str::uuid(),
                            'barcode' => $data['barcode'] ?: null,
                            'description' => $data['description'] ?: null,
                            'category' => $data['category'] ?: null,
                            'image_path' => $data['image_path'] ?: null,
                            'active' => $active,
                        ]);
                    }

                    $imported++;
                }
            });

            return redirect()->route('products.index')->with('success', "Importacion masiva completada: {$imported} productos procesados.");
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['file' => $exception->getMessage()]);
        }
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create([
            ...$request->validated(),
            'uuid' => (string) Str::uuid(),
        ]);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    private function parseBoolean(mixed $value, bool $default, int $line): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $normalized = Str::lower(trim((string) $value));

        return match ($normalized) {
            '1', 'true', 'si', 'sí', 'yes', 'activo' => true,
            '0', 'false', 'no', 'inactivo' => false,
            default => throw new InvalidArgumentException("Fila {$line}: el campo active debe ser 1 o 0."),
        };
    }
}
