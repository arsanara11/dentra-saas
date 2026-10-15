<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Queue extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Statuses
    |--------------------------------------------------------------------------
    */

    public const STATUS_WAITING = 'waiting';
    public const STATUS_CALLED = 'called';
    public const STATUS_SERVING = 'serving';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_SKIPPED = 'skipped';
    public const STATUS_NO_SHOW = 'no_show';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_CALLED,
        self::STATUS_SERVING,
        self::STATUS_COMPLETED,
        self::STATUS_SKIPPED,
        self::STATUS_NO_SHOW,
        self::STATUS_CANCELLED,
    ];

    /** Statuses that end the ticket's life. */
    public const FINAL_STATUSES = [
        self::STATUS_COMPLETED,
        self::STATUS_NO_SHOW,
        self::STATUS_CANCELLED,
    ];

    /** Statuses that still occupy the clinic flow. */
    public const ACTIVE_STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_CALLED,
        self::STATUS_SERVING,
        self::STATUS_SKIPPED,
    ];

    /*
    |--------------------------------------------------------------------------
    | Priorities
    |--------------------------------------------------------------------------
    */

    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_PRIORITY = 'priority';
    public const PRIORITY_EMERGENCY = 'emergency';

    public const PRIORITIES = [
        self::PRIORITY_NORMAL,
        self::PRIORITY_PRIORITY,
        self::PRIORITY_EMERGENCY,
    ];

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'doctor_id',
        'patient_id',
        'appointment_id',
        'queue_date',
        'prefix',
        'queue_number',
        'priority',
        'status',
        'called_at',
        'started_at',
        'completed_at',
        'skipped_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'called_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'skipped_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(QueueEvent::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Display number such as A-001.
     */
    public function getTicketNumberAttribute(): string
    {
        return sprintf(
            '%s-%03d',
            $this->prefix ?: 'A',
            (int) $this->queue_number
        );
    }

    public function isFinal(): bool
    {
        return in_array($this->status, self::FINAL_STATUSES, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForTenant(
        Builder $query,
        int $tenantId
    ): Builder {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeOnDate(
        Builder $query,
        string $date
    ): Builder {
        return $query->whereDate('queue_date', $date);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }
}