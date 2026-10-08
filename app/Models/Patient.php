<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'patient_number',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'allergies',
        'medical_notes',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' . ($this->last_name ?? '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | Patient Number Generator
    |--------------------------------------------------------------------------
    */

    public static function generatePatientNumber(int $tenantId): string
    {
        $lastPatient = static::withTrashed()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastPatient
            ? ((int) preg_replace('/\D/', '', $lastPatient->patient_number)) + 1
            : 1;

        return 'PAT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}