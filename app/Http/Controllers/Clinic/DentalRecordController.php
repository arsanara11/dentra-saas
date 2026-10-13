<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DentalRecord;
use App\Models\Doctor;
use App\Models\Patient;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DentalRecordController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Display all dental records for the active tenant.
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            auth()->user()->hasPermission('dental_records.view', $tenant),
            403
        );

        $query = DentalRecord::where('tenant_id', $tenant->id)
            ->with(['patient', 'doctor', 'creator']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where(function ($nameQuery) use ($search) {
                        $nameQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhereRaw(
                                "CONCAT(first_name, ' ', last_name) LIKE ?",
                                ["%{$search}%"]
                            )
                            ->orWhere('patient_number', 'like', "%{$search}%");
                    });
                })
                ->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                    $doctorQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('diagnosis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('examined_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('examined_at', '<=', $request->date_to);
        }

        $records = $query->latest('examined_at')
            ->paginate(10)
            ->withQueryString();

        return view('clinic.dental-records.index', compact('records'));
    }

    /**
     * Show the form for creating a dental record.
     */
    public function create()
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            auth()->user()->hasPermission('dental_records.create', $tenant),
            403
        );

        $patients = Patient::where('tenant_id', $tenant->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $doctors = Doctor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $appointments = Appointment::where('tenant_id', $tenant->id)
            ->latest()
            ->get();

        return view('clinic.dental-records.create', compact(
            'patients',
            'doctors',
            'appointments'
        ));
    }

    /**
     * Store a new dental record.
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            auth()->user()->hasPermission('dental_records.create', $tenant),
            403
        );

        $validated = $this->validateRecord($request, $tenant->id);

        DentalRecord::create([
            ...$validated,
            'tenant_id' => $tenant->id,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('clinic.dental-records.index')
            ->with('success', 'Dental record berhasil ditambahkan.');
    }

    /**
     * Display a specific dental record.
     */
    public function show(DentalRecord $dentalRecord)
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            auth()->user()->hasPermission('dental_records.view', $tenant),
            403
        );

        abort_unless(
            (int) $dentalRecord->tenant_id === (int) $tenant->id,
            404
        );

        $record = $dentalRecord->load([
            'patient',
            'doctor',
            'appointment',
            'creator',
        ]);

        return view('clinic.dental-records.show', compact('record'));
    }

    /**
     * Show the form for editing a dental record.
     */
    public function edit(DentalRecord $dentalRecord)
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            auth()->user()->hasPermission('dental_records.update', $tenant),
            403
        );

        abort_unless(
            (int) $dentalRecord->tenant_id === (int) $tenant->id,
            404
        );

        $patients = Patient::where('tenant_id', $tenant->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $doctors = Doctor::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $appointments = Appointment::where('tenant_id', $tenant->id)
            ->latest()
            ->get();

        return view('clinic.dental-records.edit', compact(
            'dentalRecord',
            'patients',
            'doctors',
            'appointments'
        ));
    }

    /**
     * Update an existing dental record.
     */
    public function update(
        Request $request,
        DentalRecord $dentalRecord
    ) {
        $tenant = $this->tenantContext->require();

        abort_unless(
            auth()->user()->hasPermission('dental_records.update', $tenant),
            403
        );

        abort_unless(
            (int) $dentalRecord->tenant_id === (int) $tenant->id,
            404
        );

        $validated = $this->validateRecord($request, $tenant->id);

        $dentalRecord->update($validated);

        return redirect()
            ->route('clinic.dental-records.show', $dentalRecord)
            ->with('success', 'Dental record berhasil diperbarui.');
    }

    /**
     * Deletion of dental records is prohibited.
     */
    public function destroy(DentalRecord $dentalRecord)
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            (int) $dentalRecord->tenant_id === (int) $tenant->id,
            404
        );

        abort(403, 'Penghapusan rekam medis tidak diizinkan.');
    }

    /**
     * Validate dental record input.
     */
    private function validateRecord(
        Request $request,
        int $tenantId
    ): array {
        return $request->validate([
            'patient_id' => [
                'required',
                'integer',
                Rule::exists('patients', 'id')
                    ->where('tenant_id', $tenantId),
            ],

            'doctor_id' => [
                'required',
                'integer',
                Rule::exists('doctors', 'id')
                    ->where('tenant_id', $tenantId),
            ],

            'appointment_id' => [
                'nullable',
                'integer',
                Rule::exists('appointments', 'id')
                    ->where('tenant_id', $tenantId),
            ],

            'examined_at' => ['required', 'date'],
            'chief_complaint' => ['nullable', 'string', 'max:5000'],
            'diagnosis' => ['nullable', 'string', 'max:5000'],
            'treatment' => ['nullable', 'string', 'max:5000'],
            'prescription' => ['nullable', 'string', 'max:5000'],
            'clinical_notes' => ['nullable', 'string', 'max:10000'],
            'next_visit_at' => ['nullable', 'date'],
        ]);
    }
}