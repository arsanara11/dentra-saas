<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Patient;
use App\Support\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    public function index(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.view', $tenant),
            403
        );

        $search = trim((string) $request->input('search'));
        $status = $request->input('status');
        $date = $request->input('date');

        $appointments = Appointment::query()
            ->where('tenant_id', $tenant->id)
            ->with(['patient', 'branch'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('appointment_number', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('patient_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                in_array($status, [
                    'scheduled',
                    'confirmed',
                    'completed',
                    'cancelled',
                    'no_show',
                ], true),
                fn ($query) => $query->where('status', $status)
            )
            ->when(
                $date && strtotime($date),
                fn ($query) => $query->whereDate('appointment_date', $date)
            )
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        return view('clinic.appointments.index', compact(
            'tenant',
            'appointments',
            'search',
            'status',
            'date'
        ));
    }

    public function create(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.create', $tenant),
            403
        );

        $patients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $branches = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->get();

        return view('clinic.appointments.create', compact(
            'tenant',
            'patients',
            'branches'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.create', $tenant),
            403
        );

        $validated = $this->validateAppointment($request, $tenant->id);

        $validated['tenant_id'] = $tenant->id;
        $validated['appointment_number'] = $this->generateAppointmentNumber(
            $tenant->id
        );
        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'scheduled';

        $appointment = Appointment::create($validated);

        return redirect()
            ->route('clinic.appointments.show', $appointment)
            ->with('success', 'Appointment created successfully.');
    }

    public function show(
        Request $request,
        Appointment $appointment
    ): View {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.view', $tenant),
            403
        );

        abort_unless(
            $appointment->tenant_id === $tenant->id,
            404
        );

        $appointment->load(['patient', 'branch', 'creator']);

        return view('clinic.appointments.show', compact(
            'tenant',
            'appointment'
        ));
    }

    public function edit(
        Request $request,
        Appointment $appointment
    ): View {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.update', $tenant),
            403
        );

        abort_unless(
            $appointment->tenant_id === $tenant->id,
            404
        );

        $patients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $branches = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->get();

        return view('clinic.appointments.edit', compact(
            'tenant',
            'appointment',
            'patients',
            'branches'
        ));
    }

    public function update(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.update', $tenant),
            403
        );

        abort_unless(
            $appointment->tenant_id === $tenant->id,
            404
        );

        $validated = $this->validateAppointment(
            $request,
            $tenant->id
        );

        $validated['status'] = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'scheduled',
                    'confirmed',
                    'completed',
                    'cancelled',
                    'no_show',
                ]),
            ],
        ])['status'];

        $appointment->update($validated);

        return redirect()
            ->route('clinic.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.delete', $tenant),
            403
        );

        abort_unless(
            $appointment->tenant_id === $tenant->id,
            404
        );

        $appointment->delete();

        return redirect()
            ->route('clinic.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    private function validateAppointment(
        Request $request,
        int $tenantId
    ): array {
        return $request->validate([
            'patient_id' => [
                'required',
                Rule::exists('patients', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'active'),
            ],
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')
                    ->where('tenant_id', $tenantId),
            ],
            'appointment_date' => [
                'required',
                'date',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after:start_time',
            ],
            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    private function generateAppointmentNumber(int $tenantId): string
    {
        do {
            $number = 'APT-' . now()->format('Ymd') . '-'
                . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (
            Appointment::where('tenant_id', $tenantId)
                ->where('appointment_number', $number)
                ->exists()
        );

        return $number;
    }
}

