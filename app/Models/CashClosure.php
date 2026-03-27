<?php

namespace App\Models;

use App\Models\Concerns\HasSyncVersion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashClosure extends Model
{
    use HasFactory, HasSyncVersion;

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_RECONCILED = 'reconciled';

    public const SOURCE_SERVER = 'server';
    public const SOURCE_POS = 'pos';
    public const SOURCE_SYSTEM = 'system';

    protected $fillable = [
        'uuid',
        'device_id',
        'user_id',
        'opening_amount',
        'cash_sales',
        'card_sales',
        'transfer_sales',
        'total_sales',
        'expected_amount',
        'closing_amount',
        'difference',
        'status',
        'source',
        'notes',
        'opened_at',
        'closed_at',
        'client_generated_at',
        'received_at',
        'sync_version',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'cash_sales' => 'decimal:2',
            'card_sales' => 'decimal:2',
            'transfer_sales' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'expected_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'client_generated_at' => 'datetime',
            'received_at' => 'datetime',
            'sync_version' => 'int',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
