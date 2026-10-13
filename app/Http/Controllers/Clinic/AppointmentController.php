<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Patient;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Display appointment directory.
     */
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

        $query = Appointment::query()
            ->where('tenant_id', $tenant->id)
            ->with(['patient', 'branch']);

        $this->applyFilters($query, $search, $status, $date);

        // Statistik dihitung dari seluruh data yang cocok dengan filter,
        // bukan hanya 15 data pada halaman aktif.
        $statisticsQuery = clone $query;

        $statistics = [
            'total' => (clone $statisticsQuery)->count(),
            'scheduled' => (clone $statisticsQuery)
                ->where('status', 'scheduled')
                ->count(),
            'confirmed' => (clone $statisticsQuery)
                ->where('status', 'confirmed')
                ->count(),
            'completed' => (clone $statisticsQuery)
                ->where('status', 'completed')
                ->count(),
        ];

        $appointments = $query
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        return view('clinic.appointments.index', compact(
            'tenant',
            'appointments',
            'search',
            'status',
            'date',
            'statistics'
        ));
    }

    /**
     * Show appointment creation form.
     */
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
            ->orderBy('name')
            ->get();

        return view('clinic.appointments.create', compact(
            'tenant',
            'patients',
            'branches'
        ));
    }

    /**
     * Store a new appointment.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.create', $tenant),
            403
        );

        $validated = $this->validateAppointment(
            $request,
            (int) $tenant->id
        );

        $this->ensureNoPatientScheduleConflict(
            $validated,
            (int) $tenant->id
        );

        $appointment = DB::transaction(function () use (
            $validated,
            $tenant,
            $request
        ) {
            $validated['tenant_id'] = $tenant->id;
            $validated['appointment_number'] =
                $this->generateAppointmentNumber((int) $tenant->id);
            $validated['created_by'] = $request->user()->id;
            $validated['status'] = 'scheduled';

            return Appointment::create($validated);
        });

        return redirect()
            ->route('clinic.appointments.show', $appointment)
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Display appointment details.
     */
    public function show(
        Request $request,
        Appointment $appointment
    ): View {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.view', $tenant),
            403
        );

        $this->ensureAppointmentBelongsToTenant(
            $appointment,
            (int) $tenant->id
        );

        $appointment->load(['patient', 'branch', 'creator']);

        return view('clinic.appointments.show', compact(
            'tenant',
            'appointment'
        ));
    }

    /**
     * Show appointment edit form.
     */
    public function edit(
        Request $request,
        Appointment $appointment
    ): View {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.update', $tenant),
            403
        );

        $this->ensureAppointmentBelongsToTenant(
            $appointment,
            (int) $tenant->id
        );

        $patients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $branches = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        return view('clinic.appointments.edit', compact(
            'tenant',
            'appointment',
            'patients',
            'branches'
        ));
    }

    /**
     * Update an appointment.
     */
    public function update(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.update', $tenant),
            403
        );

        $this->ensureAppointmentBelongsToTenant(
            $appointment,
            (int) $tenant->id
        );

        $validated = $this->validateAppointment(
            $request,
            (int) $tenant->id
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

        $this->ensureNoPatientScheduleConflict(
            $validated,
            (int) $tenant->id,
            (int) $appointment->id
        );

        $appointment->update($validated);

        return redirect()
            ->route('clinic.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Delete an appointment when the user has permission.
     */
    public function destroy(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('appointments.delete', $tenant),
            403
        );

        $this->ensureAppointmentBelongsToTenant(
            $appointment,
            (int) $tenant->id
        );

        $appointment->delete();

        return redirect()
            ->route('clinic.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Apply directory filters.
     */
    private function applyFilters(
        Builder $query,
        string $search,
        ?string $status,
        ?string $date
    ): void {
        $query
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('appointment_number', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('patient', function (Builder $patientQuery) use ($search) {
                            $patientQuery->where(function (Builder $nameQuery) use ($search) {
                                $nameQuery
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('patient_number', 'like', "%{$search}%")
                                    ->orWhereRaw(
                                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                                        ["%{$search}%"]
                                    );
                            });
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
                fn (Builder $query) => $query->where('status', $status)
            )
            ->when(
                $date !== null && $date !== '',
                fn (Builder $query) => $query->whereDate(
                    'appointment_date',
                    $date
                )
            );
    }

    /**
     * Validate appointment form data.
     */
    private function validateAppointment(
        Request $request,
        int $tenantId
    ): array {
        return $request->validate([
            'patient_id' => [
                'required',
                'integer',
                Rule::exists('patients', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'active'),
            ],

            'branch_id' => [
                'nullable',
                'integer',
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
                'max:10000',
            ],
        ]);
    }

    /**
     * Prevent overlapping appointments for the same patient.
     *
     * The check runs when both the new and existing appointment
     * have an end time. Cancelled and no-show appointments are ignored.
     */
    private function ensureNoPatientScheduleConflict(
        array $data,
        int $tenantId,
        ?int $ignoreAppointmentId = null
    ): void {
        if (empty($data['end_time'])) {
            return;
        }

        $conflict = Appointment::query()
            ->where('tenant_id', $tenantId)
            ->where('patient_id', $data['patient_id'])
            ->whereDate('appointment_date', $data['appointment_date'])
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->whereNotNull('end_time')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->when(
                $ignoreAppointmentId !== null,
                fn (Builder $query) => $query->where(
                    'id',
                    '!=',
                    $ignoreAppointmentId
                )
            )
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_time' => 'This patient already has an appointment that overlaps with the selected time.',
            ]);
        }
    }

    /**
     * Ensure the appointment belongs to the active tenant.
     */
    private function ensureAppointmentBelongsToTenant(
        Appointment $appointment,
        int $tenantId
    ): void {
        abort_unless(
            (int) $appointment->tenant_id === $tenantId,
            404
        );
    }

    /**
     * Generate a unique appointment number per tenant.
     */
    private function generateAppointmentNumber(int $tenantId): string
    {
        do {
            $number = 'APT-' . now()->format('Ymd') . '-'
                . strtoupper(bin2hex(random_bytes(4)));
        } while (
            Appointment::query()
                ->where('tenant_id', $tenantId)
                ->where('appointment_number', $number)
                ->exists()
        );

        return $number;
    }
}