<?php

namespace App\Services\Sync;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class SyncQueryService
{
    public function resolve(Builder $query, Request $request, array $options = []): array
    {
        $column = $options['order_column'] ?? 'updated_at';
        $secondaryColumn = $options['secondary_order_column'] ?? 'id';
        $perPage = min(max((int) $request->integer('per_page', $options['default_per_page'] ?? 50), 1), (int) ($options['max_per_page'] ?? 200));
        $supportsSoftDeletes = (bool) ($options['supports_soft_deletes'] ?? false);
        $preferCursor = (bool) ($options['prefer_cursor'] ?? false);
        $defaultIncludeDeleted = (bool) ($options['include_deleted'] ?? false);
        $alwaysPaginate = (bool) ($options['always_paginate'] ?? false);

        if ($request->filled('updated_since')) {
            $query->where($column, '>=', Carbon::parse((string) $request->input('updated_since')));
        }

        if ($supportsSoftDeletes && method_exists($query->getModel(), 'bootSoftDeletes')) {
            if ($request->boolean('include_deleted', $defaultIncludeDeleted)) {
                $query->withTrashed();
            }
        }

        $query->orderBy($column)->orderBy($secondaryColumn);

        if ($request->filled('cursor') || ($preferCursor && ($request->filled('per_page') || $alwaysPaginate))) {
            $paginator = $query->cursorPaginate($perPage, ['*'], 'cursor', $request->query('cursor'));

            return [
                'records' => $paginator->items(),
                'meta' => $this->cursorMeta($paginator, $request, $perPage),
            ];
        }

        if ($request->filled('per_page') || $alwaysPaginate) {
            $paginator = $query->paginate($perPage);

            return [
                'records' => $paginator->items(),
                'meta' => $this->lengthAwareMeta($paginator, $request),
            ];
        }

        $records = $query->get();

        return [
            'records' => $records,
            'meta' => [
                'pagination' => [
                    'type' => 'none',
                    'count' => $records->count(),
                ],
                'filters' => $this->filtersMeta($request),
            ],
        ];
    }

    private function cursorMeta(AbstractCursorPaginator $paginator, Request $request, int $perPage): array
    {
        return [
            'pagination' => [
                'type' => 'cursor',
                'per_page' => $perPage,
                'count' => count($paginator->items()),
                'has_more_pages' => $paginator->hasMorePages(),
                'next_cursor' => $paginator->nextCursor()?->encode(),
                'prev_cursor' => $paginator->previousCursor()?->encode(),
            ],
            'filters' => $this->filtersMeta($request),
        ];
    }

    private function lengthAwareMeta(LengthAwarePaginator $paginator, Request $request): array
    {
        return [
            'pagination' => [
                'type' => 'page',
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'count' => count($paginator->items()),
            ],
            'filters' => $this->filtersMeta($request),
        ];
    }

    private function filtersMeta(Request $request): array
    {
        return [
            'updated_since' => $request->input('updated_since'),
            'cursor' => $request->input('cursor'),
            'include_deleted' => $request->boolean('include_deleted'),
        ];
    }
}
