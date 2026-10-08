<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    /**
     * Display a listing of patients.
     */
    public function index(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('patients.view', $tenant),
            403
        );

        $search = trim((string) $request->input('search'));

        $patients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('patient_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clinic.patients.index', [
            'tenant' => $tenant,
            'patients' => $patients,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('patients.create', $tenant),
            403
        );

        return view('clinic.patients.create', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Store a newly created patient.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('patients.create', $tenant),
            403
        );

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],
            'allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['patient_number'] = Patient::generatePatientNumber(
            $tenant->id
        );
        $validated['status'] = 'active';

        $patient = Patient::create($validated);

        return redirect()
            ->route('clinic.patients.show', $patient)
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified patient.
     */
    public function show(Request $request, Patient $patient): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('patients.view', $tenant),
            403
        );

        abort_unless(
            $patient->tenant_id === $tenant->id,
            404
        );

        return view('clinic.patients.show', [
            'tenant' => $tenant,
            'patient' => $patient,
        ]);
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Request $request, Patient $patient): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('patients.update', $tenant),
            403
        );

        abort_unless(
            $patient->tenant_id === $tenant->id,
            404
        );

        return view('clinic.patients.edit', [
            'tenant' => $tenant,
            'patient' => $patient,
        ]);
    }

    /**
     * Update the specified patient.
     */
    public function update(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('patients.update', $tenant),
            403
        );

        abort_unless(
            $patient->tenant_id === $tenant->id,
            404
        );

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],
            'allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        return redirect()
            ->route('clinic.patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient.
     */
    public function destroy(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('patients.delete', $tenant),
            403
        );

        abort_unless(
            $patient->tenant_id === $tenant->id,
            404
        );

        $patient->delete();

        return redirect()
            ->route('clinic.patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
}