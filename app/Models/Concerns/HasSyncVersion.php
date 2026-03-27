<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasSyncVersion
{
    public static function bootHasSyncVersion(): void
    {
        static::creating(function (Model $model): void {
            if (! $model->getAttribute('sync_version')) {
                $model->setAttribute('sync_version', 1);
            }
        });

        static::updating(function (Model $model): void {
            if ($model->isDirty('sync_version')) {
                return;
            }

            $model->setAttribute('sync_version', max(1, (int) $model->getOriginal('sync_version', 1)) + 1);
        });

        if (method_exists(static::class, 'restoring')) {
            static::restoring(function (Model $model): void {
                $model->setAttribute('sync_version', max(1, (int) $model->getOriginal('sync_version', 1)) + 1);
            });
        }
    }
}
