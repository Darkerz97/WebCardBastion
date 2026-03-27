<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;

trait ApiResponse
{
    protected function successResponse(mixed $data = null, string $message = 'OK', int $status = 200, array $meta = []): JsonResponse
    {
        [$normalizedData, $normalizedMeta] = $this->normalizePayload($data, $meta);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $normalizedData,
            'meta' => $normalizedMeta,
            'errors' => [],
        ], $status);
    }

    protected function errorResponse(string $message, int $status = 422, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [],
            'errors' => $errors,
        ], $status);
    }

    private function normalizePayload(mixed $data, array $meta): array
    {
        if (! $data instanceof Responsable) {
            return [$data, $meta];
        }

        $payload = json_decode($data->toResponse(request())->getContent(), true);

        if (! is_array($payload)) {
            return [$data, $meta];
        }

        $resourceMeta = [];

        if (isset($payload['meta']) && is_array($payload['meta'])) {
            $resourceMeta = array_replace_recursive($resourceMeta, $payload['meta']);
        }

        if (isset($payload['links']) && is_array($payload['links'])) {
            $resourceMeta['links'] = $payload['links'];
        }

        return [
            $payload['data'] ?? $payload,
            array_replace_recursive($resourceMeta, $meta),
        ];
    }
}
