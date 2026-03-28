<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Support\CsvReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->search($request->string('search')->toString())
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')))
            ->withCount('sales')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create', [
            'customer' => new Customer(),
            'users' => $this->availableUsers(),
        ]);
    }

    public function template(): StreamedResponse
    {
        $headers = ['name', 'phone', 'email', 'notes', 'credit_balance', 'active'];
        $rows = [
            ['Juan Perez', '5512345678', 'juan@example.com', 'Cliente frecuente', '0.00', '1'],
        ];

        return response()->streamDownload(function () use ($headers, $rows): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, 'plantilla_clientes.csv', [
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
                throw new InvalidArgumentException('La plantilla de clientes no contiene filas para importar.');
            }

            $imported = 0;

            DB::transaction(function () use ($rows, &$imported): void {
                foreach ($rows as $row) {
                    $line = $row['_row'];

                    $validator = Validator::make($row, [
                        'name' => ['required', 'string', 'max:255'],
                        'phone' => ['nullable', 'string', 'max:50'],
                        'email' => ['nullable', 'email', 'max:255'],
                        'notes' => ['nullable', 'string'],
                        'credit_balance' => ['nullable', 'numeric', 'min:0'],
                        'active' => ['nullable'],
                    ]);

                    if ($validator->fails()) {
                        throw new InvalidArgumentException("Fila {$line}: ".$validator->errors()->first());
                    }

                    $data = $validator->validated();
                    $customer = $this->findCustomerForImport($data['email'] ?? null, $data['phone'] ?? null);

                    if (! $customer && empty($data['email']) && empty($data['phone'])) {
                        $customer = Customer::withTrashed()->where('name', $data['name'])->first();
                    }

                    if (! empty($data['email'])) {
                        $emailOwner = Customer::withTrashed()
                            ->where('email', $data['email'])
                            ->when($customer, fn ($query) => $query->where('id', '!=', $customer->id))
                            ->first();

                        if ($emailOwner) {
                            throw new InvalidArgumentException("Fila {$line}: el correo {$data['email']} ya pertenece a otro cliente.");
                        }
                    }

                    $active = $this->parseBoolean($data['active'] ?? null, true, $line);

                    if ($customer) {
                        if ($customer->trashed()) {
                            $customer->restore();
                        }

                        $customer->update([
                            ...$data,
                            'phone' => $data['phone'] ?: null,
                            'email' => $data['email'] ?: null,
                            'notes' => $data['notes'] ?: null,
                            'credit_balance' => $data['credit_balance'] ?: 0,
                            'active' => $active,
                        ]);
                    } else {
                        Customer::create([
                            ...$data,
                            'uuid' => (string) Str::uuid(),
                            'phone' => $data['phone'] ?: null,
                            'email' => $data['email'] ?: null,
                            'notes' => $data['notes'] ?: null,
                            'credit_balance' => $data['credit_balance'] ?: 0,
                            'active' => $active,
                        ]);
                    }

                    $imported++;
                }
            });

            return redirect()->route('customers.index')->with('success', "Importacion masiva completada: {$imported} clientes procesados.");
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['file' => $exception->getMessage()]);
        }
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        Customer::create([
            ...$request->validated(),
            'uuid' => (string) Str::uuid(),
            'credit_balance' => $request->validated('credit_balance', 0),
        ]);

        return redirect()->route('customers.index')->with('success', 'Cliente creado correctamente.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'user.role',
            'sales' => fn ($query) => $query->latest('sold_at')->limit(10),
            'preorders' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer,
            'users' => $this->availableUsers($customer),
        ]);
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update([
            ...$request->validated(),
            'credit_balance' => $request->validated('credit_balance', $customer->credit_balance),
        ]);

        return redirect()->route('customers.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Cliente eliminado correctamente.');
    }

    private function findCustomerForImport(?string $email, ?string $phone): ?Customer
    {
        $emailCustomer = $email ? Customer::withTrashed()->where('email', $email)->first() : null;
        $phoneCustomer = $phone ? Customer::withTrashed()->where('phone', $phone)->first() : null;

        if ($emailCustomer && $phoneCustomer && $emailCustomer->id !== $phoneCustomer->id) {
            throw new InvalidArgumentException('La plantilla referencia un correo y un telefono que pertenecen a clientes distintos.');
        }

        return $emailCustomer ?? $phoneCustomer;
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

    private function availableUsers(?Customer $customer = null)
    {
        return User::query()
            ->with('role')
            ->where(function ($query) use ($customer): void {
                $query->whereDoesntHave('customer');

                if ($customer?->user_id) {
                    $query->orWhere('id', $customer->user_id);
                }
            })
            ->orderBy('name')
            ->get();
    }
}
