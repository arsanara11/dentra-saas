<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DentalRecord extends Model
{
    protected $fillable = [
        'tenant_id',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'examined_at',
        'chief_complaint',
        'diagnosis',
        'treatment',
        'prescription',
        'clinical_notes',
        'next_visit_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'examined_at' => 'datetime',
            'next_visit_at' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
