<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceRequest;
use App\Http\Requests\Sync\SyncIndexRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Services\Sync\SyncQueryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SyncQueryService $syncQueryService)
    {
    }

    public function index(SyncIndexRequest $request): JsonResponse
    {
        $query = Device::query()
            ->search($request->string('search')->toString())
            ->when($request->filled('active'), fn ($query) => $query->where('active', $request->boolean('active')));

        $result = $this->syncQueryService->resolve($query, $request, [
            'supports_soft_deletes' => false,
            'order_column' => 'updated_at',
            'always_paginate' => true,
            'default_per_page' => 15,
        ]);

        return $this->successResponse(DeviceResource::collection($result['records']), 'Dispositivos obtenidos correctamente.', meta: $result['meta']);
    }

    public function store(DeviceRequest $request): JsonResponse
    {
        $device = Device::create([
            ...$request->validated(),
            'uuid' => (string) Str::uuid(),
        ]);

        return $this->successResponse(new DeviceResource($device), 'Dispositivo creado correctamente.', 201);
    }

    public function show(Device $device): JsonResponse
    {
        return $this->successResponse(new DeviceResource($device), 'Dispositivo obtenido correctamente.');
    }

    public function update(DeviceRequest $request, Device $device): JsonResponse
    {
        $device->update($request->validated());

        return $this->successResponse(new DeviceResource($device->refresh()), 'Dispositivo actualizado correctamente.');
    }

    public function destroy(Device $device): JsonResponse
    {
        $device->delete();

        return $this->successResponse(null, 'Dispositivo eliminado correctamente.');
    }
}
