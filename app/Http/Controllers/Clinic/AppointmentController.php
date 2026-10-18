<?php

namespace App\Http\Controllers\Clinic;

use App\Exceptions\QueueException;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Service;
use App\Services\Queue\QueueService;
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
        protected TenantContext $tenantContext,
        protected QueueService $queueService
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
            ->with(['patient', 'branch', 'doctor', 'service']);

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

        $doctors = $this->activeDoctors((int) $tenant->id);
        $services = $this->activeServices((int) $tenant->id);

        return view('clinic.appointments.create', compact(
            'tenant',
            'patients',
            'branches',
            'doctors',
            'services'
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

        $appointment = DB::transaction(function () use (
            $validated,
            $tenant,
            $request
        ) {
            // The service may fill in the end time, so it is applied before
            // any schedule conflict is checked.
            $validated = $this->applyService(
                $validated,
                (int) $tenant->id
            );

            // Locks the doctor row, so two receptionists booking the same
            // doctor at the same moment cannot both pass the checks below.
            $validated = $this->resolveDoctorAndBranch(
                $validated,
                (int) $tenant->id
            );

            $this->ensureNoPatientScheduleConflict(
                $validated,
                (int) $tenant->id
            );

            $this->ensureNoDoctorScheduleConflict(
                $validated,
                (int) $tenant->id
            );

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

        $appointment->load(['patient', 'branch', 'doctor', 'service', 'creator', 'queue']);

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

        // The current doctor and service stay selectable even if they were
        // deactivated later, so saving the form never clears them silently.
        $doctors = $this->activeDoctors(
            (int) $tenant->id,
            $appointment->doctor_id ? (int) $appointment->doctor_id : null
        );
        $services = $this->activeServices(
            (int) $tenant->id,
            $appointment->service_id ? (int) $appointment->service_id : null
        );

        return view('clinic.appointments.edit', compact(
            'tenant',
            'appointment',
            'patients',
            'branches',
            'doctors',
            'services'
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

        $this->ensureNotCheckedIn($appointment);

        $validated = $this->validateAppointment(
            $request,
            (int) $tenant->id,
            $appointment->doctor_id ? (int) $appointment->doctor_id : null,
            $appointment->service_id ? (int) $appointment->service_id : null
        );

        $validated['status'] = $request->validate([
            'status' => [
                'required',
                Rule::in(Appointment::MANUAL_STATUSES),
            ],
        ])['status'];

        DB::transaction(function () use ($validated, $tenant, $appointment) {
            $validated = $this->applyService(
                $validated,
                (int) $tenant->id,
                $appointment->service_id ? (int) $appointment->service_id : null
            );

            $validated = $this->resolveDoctorAndBranch(
                $validated,
                (int) $tenant->id,
                $appointment->doctor_id ? (int) $appointment->doctor_id : null
            );

            $this->ensureNoPatientScheduleConflict(
                $validated,
                (int) $tenant->id,
                (int) $appointment->id
            );

            $this->ensureNoDoctorScheduleConflict(
                $validated,
                (int) $tenant->id,
                (int) $appointment->id
            );

            $appointment->update($validated);
        });

        return redirect()
            ->route('clinic.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Check a patient in for their appointment.
     *
     * Issues a queue ticket and marks the appointment as checked in.
     */
    public function checkIn(
        Request $request,
        Appointment $appointment
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();
        $user = $request->user();

        abort_unless($user->hasPermission('appointments.view', $tenant), 403);
        abort_unless($user->hasPermission('queues.create', $tenant), 403);

        $this->ensureAppointmentBelongsToTenant(
            $appointment,
            (int) $tenant->id
        );

        $validated = $request->validate([
            'doctor_id' => ['nullable', 'integer'],
            'priority' => ['nullable', Rule::in(Queue::PRIORITIES)],
        ]);

        // Jumping the line needs the right to manage the queue.
        if (
            ($validated['priority'] ?? Queue::PRIORITY_NORMAL) !== Queue::PRIORITY_NORMAL
        ) {
            abort_unless($user->hasPermission('queues.update', $tenant), 403);
        }

        try {
            $queue = $this->queueService->checkInAppointment(
                $tenant,
                $user,
                (int) $appointment->id,
                $validated
            );
        } catch (QueueException $exception) {
            return back()->withErrors(['appointment' => $exception->getMessage()]);
        }

        return back()->with(
            'success',
            "Patient checked in. Queue ticket {$queue->ticket_number} has been issued."
        );
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

        $this->ensureNotCheckedIn($appointment);

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
                in_array($status, Appointment::STATUSES, true),
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
        int $tenantId,
        ?int $keepDoctorId = null,
        ?int $keepServiceId = null
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

            // The value already saved on the appointment is always accepted,
            // even if that service or doctor has been deactivated since.
            'service_id' => [
                'nullable',
                'integer',
                Rule::exists('services', 'id')->where(
                    function ($query) use ($tenantId, $keepServiceId) {
                        $query->where('tenant_id', $tenantId)
                            ->where(function ($query) use ($keepServiceId) {
                                $query->where('status', Service::STATUS_ACTIVE)
                                    ->orWhere('id', $keepServiceId ?? 0);
                            });
                    }
                ),
            ],

            'doctor_id' => [
                'nullable',
                'integer',
                Rule::exists('doctors', 'id')->where(
                    function ($query) use ($tenantId, $keepDoctorId) {
                        $query->where('tenant_id', $tenantId)
                            ->whereNull('deleted_at')
                            ->where(function ($query) use ($keepDoctorId) {
                                $query->where('status', 'active')
                                    ->orWhere('id', $keepDoctorId ?? 0);
                            });
                    }
                ),
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
     * Active doctors of the tenant for the form dropdown.
     */
    private function activeDoctors(int $tenantId, ?int $keepId = null)
    {
        return Doctor::query()
            ->where('tenant_id', $tenantId)
            ->where(function (Builder $query) use ($keepId) {
                $query->where('status', 'active')
                    ->when(
                        $keepId !== null,
                        fn (Builder $query) => $query->orWhere('id', $keepId)
                    );
            })
            ->orderBy('name')
            ->get(['id', 'branch_id', 'name', 'specialization', 'status']);
    }

    /**
     * Active services of the tenant for the form dropdown.
     */
    private function activeServices(int $tenantId, ?int $keepId = null)
    {
        return Service::query()
            ->where('tenant_id', $tenantId)
            ->where(function (Builder $query) use ($keepId) {
                $query->where('status', Service::STATUS_ACTIVE)
                    ->when(
                        $keepId !== null,
                        fn (Builder $query) => $query->orWhere('id', $keepId)
                    );
            })
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'duration_minutes', 'price', 'status']);
    }

    /**
     * Apply the chosen service to the appointment data.
     *
     * - The end time is filled from the service duration when left empty.
     * - The reason for visit defaults to the service name.
     */
    private function applyService(
        array $data,
        int $tenantId,
        ?int $keepServiceId = null
    ): array {
        if (empty($data['service_id'])) {
            $data['service_id'] = null;

            return $data;
        }

        $service = Service::query()
            ->where('tenant_id', $tenantId)
            ->where(function (Builder $query) use ($keepServiceId) {
                $query->where('status', Service::STATUS_ACTIVE)
                    ->orWhere('id', $keepServiceId ?? 0);
            })
            ->whereKey($data['service_id'])
            ->first();

        if (! $service) {
            throw ValidationException::withMessages([
                'service_id' => 'The selected service is not available.',
            ]);
        }

        if (empty($data['end_time'])) {
            [$hour, $minute] = array_map('intval', explode(':', $data['start_time']));

            $endMinutes = ($hour * 60) + $minute + $service->duration_minutes;

            if ($endMinutes >= 24 * 60) {
                throw ValidationException::withMessages([
                    'end_time' => 'This service would end after midnight. Choose an earlier start time.',
                ]);
            }

            $data['end_time'] = sprintf(
                '%02d:%02d',
                intdiv($endMinutes, 60),
                $endMinutes % 60
            );
        }

        if (empty($data['reason'])) {
            $data['reason'] = $service->name;
        }

        return $data;
    }

    /**
     * Lock the chosen doctor and keep the branch consistent with them.
     *
     * - A doctor assigned to a branch fills in an empty branch field.
     * - A doctor assigned to a different branch is rejected.
     *
     * Must run inside a transaction.
     */
    private function resolveDoctorAndBranch(
        array $data,
        int $tenantId,
        ?int $keepDoctorId = null
    ): array {
        if (empty($data['doctor_id'])) {
            $data['doctor_id'] = null;

            return $data;
        }

        $doctor = Doctor::query()
            ->where('tenant_id', $tenantId)
            ->where(function (Builder $query) use ($keepDoctorId) {
                $query->where('status', 'active')
                    ->orWhere('id', $keepDoctorId ?? 0);
            })
            ->whereKey($data['doctor_id'])
            ->lockForUpdate()
            ->first();

        if (! $doctor) {
            throw ValidationException::withMessages([
                'doctor_id' => 'The selected doctor is not available.',
            ]);
        }

        if ($doctor->branch_id) {
            if (empty($data['branch_id'])) {
                $data['branch_id'] = $doctor->branch_id;
            } elseif ((int) $data['branch_id'] !== (int) $doctor->branch_id) {
                throw ValidationException::withMessages([
                    'doctor_id' => 'This doctor works at a different branch.',
                ]);
            }
        }

        return $data;
    }

    /**
     * Prevent double-booking a doctor.
     *
     * Two appointments overlap when their time ranges intersect. When an
     * appointment has no end time it only blocks its exact start time.
     * Cancelled and no-show appointments are ignored.
     */
    private function ensureNoDoctorScheduleConflict(
        array $data,
        int $tenantId,
        ?int $ignoreAppointmentId = null
    ): void {
        if (empty($data['doctor_id'])) {
            return;
        }

        $start = $data['start_time'];
        $end = $data['end_time'] ?? null;

        $conflict = Appointment::query()
            ->where('tenant_id', $tenantId)
            ->where('doctor_id', $data['doctor_id'])
            ->whereDate('appointment_date', $data['appointment_date'])
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when(
                $ignoreAppointmentId !== null,
                fn (Builder $query) => $query->where(
                    'id',
                    '!=',
                    $ignoreAppointmentId
                )
            )
            ->where(function (Builder $query) use ($start, $end) {
                if ($end) {
                    $query
                        ->where(function (Builder $query) use ($start, $end) {
                            $query->whereNotNull('end_time')
                                ->where('start_time', '<', $end)
                                ->where('end_time', '>', $start);
                        })
                        ->orWhere(function (Builder $query) use ($start, $end) {
                            $query->whereNull('end_time')
                                ->where('start_time', '>=', $start)
                                ->where('start_time', '<', $end);
                        });

                    return;
                }

                $query
                    ->where('start_time', $start)
                    ->orWhere(function (Builder $query) use ($start) {
                        $query->whereNotNull('end_time')
                            ->where('start_time', '<=', $start)
                            ->where('end_time', '>', $start);
                    });
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'doctor_id' => 'This doctor already has an appointment that overlaps with the selected time.',
            ]);
        }
    }

    /**
     * A checked-in appointment is owned by the queue until the ticket is
     * completed, cancelled or marked as no-show.
     */
    private function ensureNotCheckedIn(Appointment $appointment): void
    {
        if ($appointment->status === Appointment::STATUS_CHECKED_IN) {
            throw ValidationException::withMessages([
                'appointment' => 'This appointment is checked in. Manage it from the Queue page.',
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