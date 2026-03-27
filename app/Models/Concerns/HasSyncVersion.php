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

            $model->setAttribute('sync_version', static::nextSyncVersion($model));
        });

        if (method_exists(static::class, 'restoring')) {
            static::restoring(function (Model $model): void {
                $model->setAttribute('sync_version', static::nextSyncVersion($model));
            });
        }

        if (method_exists(static::class, 'deleting')) {
            static::deleting(function (Model $model): void {
                if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                    return;
                }

                $nextVersion = static::nextSyncVersion($model);
                $model->setAttribute('sync_version', $nextVersion);
            });
        }

        if (method_exists(static::class, 'deleted')) {
            static::deleted(function (Model $model): void {
                if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                    return;
                }

                $model->newQueryWithoutScopes()
                    ->whereKey($model->getKey())
                    ->update(['sync_version' => static::nextSyncVersion($model)]);
            });
        }
    }

    private static function nextSyncVersion(Model $model): int
    {
        return max(
            1,
            (int) $model->getOriginal('sync_version', $model->getAttribute('sync_version') ?: 1),
        ) + 1;
    }
}
