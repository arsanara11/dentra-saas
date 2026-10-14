<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\Queue;
use App\Support\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    public function index(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('queues.view', $tenant),
            403
        );

        $date = $request->input('date', now()->toDateString());
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        $queues = Queue::query()
            ->where('tenant_id', $tenant->id)
            ->with(['patient', 'branch', 'appointment'])
            ->whereDate('queue_date', $date)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where(
                        'queue_number',
                        'like',
                        "%{$search}%"
                    )->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('patient_number', 'like', "%{$search}%");
                    });
                });
            })
            ->when(
                in_array($status, [
                    'waiting',
                    'called',
                    'serving',
                    'completed',
                    'cancelled',
                ], true),
                fn ($query) => $query->where('status', $status)
            )
            ->orderBy('branch_id')
            ->orderBy('queue_number')
            ->paginate(15)
            ->withQueryString();

        $summary = Queue::query()
            ->where('tenant_id', $tenant->id)
            ->whereDate('queue_date', $date)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting,
                SUM(CASE WHEN status = 'called' THEN 1 ELSE 0 END) as called,
                SUM(CASE WHEN status = 'serving' THEN 1 ELSE 0 END) as serving,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            ")
            ->first();

        $patients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $branches = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->get();

        return view('clinic.queues.index', compact(
            'tenant',
            'queues',
            'summary',
            'patients',
            'branches',
            'date',
            'search',
            'status'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('queues.create', $tenant),
            403
        );

        $validated = $request->validate([
            'patient_id' => [
                'required',
                Rule::exists('patients', 'id')
                    ->where('tenant_id', $tenant->id)
                    ->where('status', 'active'),
            ],
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')
                    ->where('tenant_id', $tenant->id),
            ],
            'appointment_id' => [
                'nullable',
                Rule::exists('appointments', 'id')
                    ->where('tenant_id', $tenant->id),
            ],
            'queue_date' => [
                'required',
                'date_format:Y-m-d',
            ],
        ]);

        if (!empty($validated['appointment_id'])) {
            $appointment = Appointment::query()
                ->where('tenant_id', $tenant->id)
                ->findOrFail($validated['appointment_id']);

            abort_unless(
                (int) $appointment->patient_id ===
                    (int) $validated['patient_id'],
                422,
                'The selected appointment does not belong to this patient.'
            );

            abort_unless(
                (int) $appointment->branch_id ===
                    (int) $validated['branch_id'],
                422,
                'The selected appointment belongs to a different branch.'
            );

            if (in_array(
                $appointment->status,
                ['cancelled', 'no_show'],
                true
            )) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'appointment_id' =>
                            'A cancelled or missed appointment cannot be queued.',
                    ]);
            }

            $alreadyQueued = Queue::query()
                ->where('tenant_id', $tenant->id)
                ->where('appointment_id', $appointment->id)
                ->exists();

            if ($alreadyQueued) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'appointment_id' =>
                            'This appointment already has a queue entry.',
                    ]);
            }
        }

        try {
            DB::transaction(function () use (
                $validated,
                $tenant,
                $request
            ) {
                // Lock the branch while allocating its queue number.
                Branch::query()
                    ->where('tenant_id', $tenant->id)
                    ->whereKey($validated['branch_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!empty($validated['appointment_id'])) {
                    // Serialize check-ins for the same appointment.
                    $appointment = Appointment::query()
                        ->where('tenant_id', $tenant->id)
                        ->whereKey($validated['appointment_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (in_array(
                        $appointment->status,
                        ['cancelled', 'no_show'],
                        true
                    )) {
                        abort(
                            422,
                            'A cancelled or missed appointment cannot be queued.'
                        );
                    }

                    $alreadyQueued = Queue::query()
                        ->where('tenant_id', $tenant->id)
                        ->where(
                            'appointment_id',
                            $appointment->id
                        )
                        ->exists();

                    if ($alreadyQueued) {
                        abort(
                            422,
                            'This appointment already has a queue entry.'
                        );
                    }
                }

                // Numbering starts independently for each branch and date.
                $lastNumber = Queue::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('branch_id', $validated['branch_id'])
                    ->whereDate(
                        'queue_date',
                        $validated['queue_date']
                    )
                    ->max('queue_number');

                Queue::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $validated['branch_id'],
                    'patient_id' => $validated['patient_id'],
                    'appointment_id' =>
                        $validated['appointment_id'] ?? null,
                    'queue_date' => $validated['queue_date'],
                    'queue_number' => ((int) $lastNumber) + 1,
                    'status' => 'waiting',
                    'created_by' => $request->user()->id,
                ]);
            }, 3);
        } catch (QueryException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'queue' =>
                        'The queue could not be created. Please try again.',
                ]);
        }

        return redirect()
            ->route('clinic.queues.index', [
                'date' => $validated['queue_date'],
            ])
            ->with(
                'success',
                'Patient added to the queue successfully.'
            );
    }

    public function updateStatus(
        Request $request,
        Queue $queue
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('queues.update', $tenant),
            403
        );

        abort_unless(
            (int) $queue->tenant_id === (int) $tenant->id,
            404
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'waiting',
                    'called',
                    'serving',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $newStatus = $validated['status'];
        $currentStatus = $queue->status;

        $allowedTransitions = [
            'waiting' => ['called', 'cancelled'],
            'called' => ['serving', 'waiting', 'cancelled'],
            'serving' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        abort_unless(
            in_array(
                $newStatus,
                $allowedTransitions[$currentStatus] ?? [],
                true
            ),
            422,
            'This queue status transition is not allowed.'
        );

        $timestamps = [];

        if ($newStatus === 'called') {
            $timestamps['called_at'] = now();
        }

        if ($newStatus === 'serving') {
            $timestamps['started_at'] = now();
        }

        if ($newStatus === 'completed') {
            $timestamps['completed_at'] = now();
        }

        $queue->update(array_merge([
            'status' => $newStatus,
        ], $timestamps));

        return redirect()
            ->route('clinic.queues.index', [
                'date' => $queue->queue_date->toDateString(),
            ])
            ->with(
                'success',
                'Queue status updated successfully.'
            );
    }
}
