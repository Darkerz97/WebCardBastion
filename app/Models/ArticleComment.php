<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'guest_name',
        'guest_email',
        'body',
        'is_approved',
        'approved_at',
        'ip_address',
    ];

    protected $appends = [
        'display_name',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?: $this->guest_name ?: 'Invitado';
    }
}
