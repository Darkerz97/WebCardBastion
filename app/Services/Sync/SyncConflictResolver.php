<?php

namespace App\Services\Sync;

use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Support\SyncConflictException;

class SyncConflictResolver
{
    public function ensureSaleCanBeUploaded(array $payload): void
    {
        if (! empty($payload['sale_number'])) {
            $existingByNumber = Sale::query()->where('sale_number', $payload['sale_number'])->first();

            if ($existingByNumber && $existingByNumber->uuid !== $payload['uuid']) {
                throw new SyncConflictException(
                    'El numero de venta ya existe con otro UUID en el servidor.',
                    ['sale_number' => ['El numero de venta ya fue usado por otra venta.']],
                    'duplicate_sale_number',
                );
            }
        }
    }

    public function ensureInventoryMovementCanBeUploaded(array $payload): void
    {
        // UUID duplicates are handled as idempotent skips by the upload services.
    }

    public function ensureCashClosureCanBeUploaded(array $payload): void
    {
        // UUID duplicates are handled as idempotent skips by the upload services.
    }
}
