<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'entity_type',
        'entity_uuid',
        'action',
        'status',
        'payload_json',
        'message',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
