<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

/**
 * Append-only audit record for a queue ticket.
 *
 * Rows can be created but never updated or deleted through Eloquent.
 */
class QueueEvent extends Model
{
    public const TYPE_CREATED = 'created';
    public const TYPE_CALLED = 'called';
    public const TYPE_RECALLED = 'recalled';
    public const TYPE_SERVING = 'serving';
    public const TYPE_COMPLETED = 'completed';
    public const TYPE_SKIPPED = 'skipped';
    public const TYPE_NO_SHOW = 'no_show';
    public const TYPE_CANCELLED = 'cancelled';

    public const TYPES = [
        self::TYPE_CREATED,
        self::TYPE_CALLED,
        self::TYPE_RECALLED,
        self::TYPE_SERVING,
        self::TYPE_COMPLETED,
        self::TYPE_SKIPPED,
        self::TYPE_NO_SHOW,
        self::TYPE_CANCELLED,
    ];

    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'queue_id',
        'event_type',
        'from_status',
        'to_status',
        'actor_id',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (QueueEvent $event) {
            $event->created_at ??= now();
        });

        static::updating(function () {
            throw new LogicException('Queue events are immutable.');
        });

        static::deleting(function () {
            throw new LogicException('Queue events cannot be deleted.');
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function scopeForTenant(
        Builder $query,
        int $tenantId
    ): Builder {
        return $query->where('tenant_id', $tenantId);
    }
}