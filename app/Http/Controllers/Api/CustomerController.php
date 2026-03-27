<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerRequest;
use App\Http\Requests\Sync\SyncIndexRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\Sync\SyncQueryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SyncQueryService $syncQueryService)
    {
    }

    public function index(SyncIndexRequest $request): JsonResponse
    {
        $query = Customer::query()
            ->search($request->string('search')->toString())
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')))
            ->withCount('sales');

        $result = $this->syncQueryService->resolve($query, $request, [
            'supports_soft_deletes' => true,
            'include_deleted' => false,
            'order_column' => 'updated_at',
            'always_paginate' => true,
            'default_per_page' => 15,
        ]);

        return $this->successResponse(CustomerResource::collection($result['records']), 'Clientes obtenidos correctamente.', meta: $result['meta']);
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $customer = Customer::create([
            ...$request->validated(),
            'uuid' => (string) Str::uuid(),
            'credit_balance' => $request->validated('credit_balance', 0),
        ]);

        return $this->successResponse(new CustomerResource($customer), 'Cliente creado correctamente.', 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        $customer->loadCount('sales');

        return $this->successResponse(new CustomerResource($customer), 'Cliente obtenido correctamente.');
    }

    public function update(CustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update([
            ...$request->validated(),
            'credit_balance' => $request->validated('credit_balance', $customer->credit_balance),
        ]);

        return $this->successResponse(new CustomerResource($customer->refresh()), 'Cliente actualizado correctamente.');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return $this->successResponse(null, 'Cliente eliminado correctamente.');
    }
}
