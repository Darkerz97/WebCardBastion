<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AdminProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Support\CsvReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            ->with('categoryModel')
            ->search($request->string('search')->toString())
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->orderByDesc('featured')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product([
                'active' => true,
                'featured' => false,
                'publish_to_store' => true,
                'product_type' => 'normal',
                'min_stock' => 0,
            ]),
            'categories' => Category::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function template(): StreamedResponse
    {
        $headers = [
            'name',
            'slug',
            'sku',
            'barcode',
            'category',
            'short_description',
            'description',
            'cost',
            'price',
            'stock',
            'min_stock',
            'product_type',
            'game',
            'card_name',
            'set_name',
            'set_code',
            'collector_number',
            'finish',
            'language',
            'card_condition',
            'image_path',
            'active',
            'featured',
            'publish_to_store',
        ];
        $rows = [
            ['Mica templada', 'mica-templada', 'MIC-001', '750100000001', 'Accesorios', 'Proteccion premium para el frente del display.', 'Proteccion frontal premium', '35.00', '89.00', '25', '5', 'normal', '', '', '', '', '', '', '', '', '', '1', '1', '1'],
            ['Lightning Bolt NM', 'lightning-bolt-nm', 'MTG-001', '', 'Singles', 'Carta individual lista para vitrina.', 'Lightning Bolt de coleccion para jugadores y coleccionistas.', '120.00', '180.00', '3', '2', 'single_card', 'Magic: The Gathering', 'Lightning Bolt', 'Revised Edition', '3ED', '161', 'Non-foil', 'English', 'NM', '', '1', '0', '1'],
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
                        'slug' => ['nullable', 'string', 'max:255'],
                        'sku' => ['required', 'string', 'max:100'],
                        'barcode' => ['nullable', 'string', 'max:100'],
                        'category' => ['nullable', 'string', 'max:100'],
                        'short_description' => ['nullable', 'string', 'max:280'],
                        'description' => ['nullable', 'string'],
                        'cost' => ['required', 'numeric', 'min:0'],
                        'price' => ['required', 'numeric', 'min:0'],
                        'stock' => ['required', 'integer', 'min:0'],
                        'min_stock' => ['nullable', 'integer', 'min:0'],
                        'product_type' => ['nullable', 'string', 'max:50'],
                        'game' => ['nullable', 'string', 'max:255'],
                        'card_name' => ['nullable', 'string', 'max:255'],
                        'set_name' => ['nullable', 'string', 'max:255'],
                        'set_code' => ['nullable', 'string', 'max:100'],
                        'collector_number' => ['nullable', 'string', 'max:100'],
                        'finish' => ['nullable', 'string', 'max:100'],
                        'language' => ['nullable', 'string', 'max:100'],
                        'card_condition' => ['nullable', 'string', 'max:100'],
                        'image_path' => ['nullable', 'string', 'max:255'],
                        'active' => ['nullable'],
                        'featured' => ['nullable'],
                        'publish_to_store' => ['nullable'],
                    ]);

                    if ($validator->fails()) {
                        throw new InvalidArgumentException("Fila {$line}: ".$validator->errors()->first());
                    }

                    $data = $validator->validated();
                    $active = $this->parseBoolean($data['active'] ?? null, true, $line, 'active');
                    $featured = $this->parseBoolean($data['featured'] ?? null, false, $line, 'featured');
                    $publishToStore = $this->parseBoolean($data['publish_to_store'] ?? null, true, $line, 'publish_to_store');
                    $product = Product::withTrashed()->where('sku', $data['sku'])->first();
                    $category = $this->resolveCategory($data['category'] ?? null);

                    if (! empty($data['barcode'])) {
                        $barcodeOwner = Product::withTrashed()
                            ->where('barcode', $data['barcode'])
                            ->when($product, fn ($query) => $query->where('id', '!=', $product->id))
                            ->first();

                        if ($barcodeOwner) {
                            throw new InvalidArgumentException("Fila {$line}: el barcode {$data['barcode']} ya pertenece a otro producto.");
                        }
                    }

                    $payload = [
                        'name' => $data['name'],
                        'slug' => $data['slug'] ?: Str::slug($data['name']),
                        'sku' => $data['sku'],
                        'barcode' => $data['barcode'] ?: null,
                        'category_id' => $category?->id,
                        'category' => $category?->name,
                        'short_description' => $data['short_description'] ?: null,
                        'description' => $data['description'] ?: null,
                        'cost' => $data['cost'],
                        'price' => $data['price'],
                        'stock' => $data['stock'],
                        'min_stock' => $data['min_stock'] ?? 0,
                        'product_type' => $data['product_type'] ?: 'normal',
                        'game' => $data['game'] ?: null,
                        'card_name' => $data['card_name'] ?: null,
                        'set_name' => $data['set_name'] ?: null,
                        'set_code' => $data['set_code'] ?: null,
                        'collector_number' => $data['collector_number'] ?: null,
                        'finish' => $data['finish'] ?: null,
                        'language' => $data['language'] ?: null,
                        'card_condition' => $data['card_condition'] ?: null,
                        'image_path' => $data['image_path'] ?: null,
                        'active' => $active,
                        'featured' => $featured,
                        'publish_to_store' => $publishToStore,
                    ];

                    if ($product) {
                        if ($product->trashed()) {
                            $product->restore();
                        }

                        $product->update($payload);
                    } else {
                        Product::create([
                            ...$payload,
                            'uuid' => (string) Str::uuid(),
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

    public function store(AdminProductRequest $request): RedirectResponse
    {
        $product = Product::create([
            ...$this->payload($request),
            'uuid' => (string) Str::uuid(),
        ]);

        $this->syncGalleryImages($request, $product);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        $product->load(['categoryModel', 'images']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('products.edit', [
            'product' => $product,
            'categories' => Category::query()->active()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(AdminProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->payload($request, $product));
        $this->syncGalleryImages($request, $product);

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    private function payload(AdminProductRequest $request, ?Product $product = null): array
    {
        $data = $request->validated();
        $category = ! empty($data['category_id']) ? Category::query()->find($data['category_id']) : null;
        $imagePath = $product?->image_path;

        if ($request->hasFile('cover_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $this->storeImage($request->file('cover_image'));
        }

        return [
            'name' => $data['name'],
            'slug' => $data['slug'] ?: Str::slug($data['name']),
            'sku' => $data['sku'],
            'barcode' => $data['barcode'] ?? null,
            'category_id' => $category?->id,
            'category' => $category?->name,
            'short_description' => $data['short_description'] ?? null,
            'description' => $data['description'] ?? null,
            'cost' => $data['cost'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'min_stock' => $data['min_stock'] ?? 0,
            'product_type' => $data['product_type'],
            'game' => $data['game'] ?? null,
            'card_name' => $data['card_name'] ?? null,
            'set_name' => $data['set_name'] ?? null,
            'set_code' => $data['set_code'] ?? null,
            'collector_number' => $data['collector_number'] ?? null,
            'finish' => $data['finish'] ?? null,
            'language' => $data['language'] ?? null,
            'card_condition' => $data['card_condition'] ?? null,
            'image_path' => $imagePath,
            'active' => (bool) $data['active'],
            'featured' => (bool) $data['featured'],
            'publish_to_store' => (bool) $data['publish_to_store'],
        ];
    }

    private function syncGalleryImages(AdminProductRequest $request, Product $product): void
    {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $nextSort = (int) $product->images()->max('sort_order') + 1;

        foreach ($request->file('gallery_images', []) as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $this->storeImage($file);
            $isPrimary = ! $product->images()->exists() && ! $product->image_path;

            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'sort_order' => $nextSort++,
                'is_primary' => $isPrimary,
            ]);

            if ($isPrimary) {
                $product->update(['image_path' => $path]);
            }
        }
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('products', 'public');
    }

    private function resolveCategory(?string $categoryName): ?Category
    {
        if (! $categoryName) {
            return null;
        }

        return Category::query()->firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            [
                'name' => $categoryName,
                'description' => null,
                'sort_order' => 999,
                'active' => true,
            ],
        );
    }

    private function parseBoolean(mixed $value, bool $default, int $line, string $field): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $normalized = Str::lower(trim((string) $value));

        return match ($normalized) {
            '1', 'true', 'si', 'sí', 'yes', 'activo' => true,
            '0', 'false', 'no', 'inactivo' => false,
            default => throw new InvalidArgumentException("Fila {$line}: el campo {$field} debe ser 1 o 0."),
        };
    }
}
