<?php

namespace App\Http\Controllers\Clinic;

use App\Exceptions\QueueException;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Tenant;
use App\Services\Queue\QueueService;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class QueueController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext,
        protected QueueService $queueService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('queues.view', $tenant),
            403
        );

        $date = $this->resolveDate($request->input('date'), $tenant);
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        $queues = Queue::query()
            ->where('tenant_id', $tenant->id)
            ->with(['patient', 'branch', 'doctor', 'appointment'])
            ->whereDate('queue_date', $date)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('queue_number', 'like', "%{$search}%")
                        ->orWhereHas('patient', function ($patientQuery) use ($search) {
                            $patientQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('patient_number', 'like', "%{$search}%");
                        });

                    // Searching a full ticket number such as "A-003" or "a3".
                    if (preg_match('/^([A-Za-z]{1,5})-?(\d{1,6})$/', $search, $match)) {
                        $query->orWhere(function ($ticketQuery) use ($match) {
                            $ticketQuery
                                ->where('prefix', strtoupper($match[1]))
                                ->where('queue_number', (int) $match[2]);
                        });
                    }
                });
            })
            ->when(
                is_string($status) && in_array($status, Queue::STATUSES, true),
                fn ($query) => $query->where('status', $status)
            )
            ->orderBy('prefix')
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
                SUM(CASE WHEN status = 'skipped' THEN 1 ELSE 0 END) as skipped,
                SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) as no_show,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            ")
            ->first();

        $patients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'patient_number']);

        $branches = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('is_main')
            ->orderBy('name')
            ->get();

        $doctors = Doctor::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'branch_id', 'name', 'doctor_code']);

        return view('clinic.queues.index', [
            'tenant' => $tenant,
            'queues' => $queues,
            'summary' => $summary,
            'patients' => $patients,
            'branches' => $branches,
            'doctors' => $doctors,
            'date' => $date,
            'search' => $search,
            'status' => $status,
            'priorities' => Queue::PRIORITIES,
            'queueService' => $this->queueService,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show (ticket detail + audit history)
    |--------------------------------------------------------------------------
    */

    public function show(Request $request, Queue $queue): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('queues.view', $tenant),
            403
        );

        abort_unless((int) $queue->tenant_id === (int) $tenant->id, 404);

        $queue->load([
            'patient',
            'branch',
            'doctor',
            'appointment',
            'creator',
            'updater',
            'events.actor',
        ]);

        return view('clinic.queues.show', [
            'tenant' => $tenant,
            'queue' => $queue,
            'queueService' => $this->queueService,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenantContext->require();
        $user = $request->user();

        abort_unless($user->hasPermission('queues.create', $tenant), 403);

        // Ownership of patient / branch / doctor / appointment is verified
        // inside QueueService, always scoped to the active tenant.
        $validated = $request->validate([
            'patient_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'doctor_id' => ['nullable', 'integer'],
            'appointment_id' => ['nullable', 'integer'],
            'queue_date' => ['nullable', 'date_format:Y-m-d'],
            'priority' => ['nullable', Rule::in(Queue::PRIORITIES)],
        ]);

        // Jumping the line needs the right to manage the queue.
        if (
            ($validated['priority'] ?? Queue::PRIORITY_NORMAL) !== Queue::PRIORITY_NORMAL
        ) {
            abort_unless($user->hasPermission('queues.update', $tenant), 403);
        }

        try {
            $queue = $this->queueService->enqueue($tenant, $user, $validated);
        } catch (QueueException $exception) {
            return back()
                ->withInput()
                ->withErrors(['queue' => $exception->getMessage()]);
        }

        return redirect()
            ->route('clinic.queues.index', [
                'date' => $queue->queue_date->toDateString(),
            ])
            ->with('success', "Ticket {$queue->ticket_number} has been issued.");
    }

    /*
    |--------------------------------------------------------------------------
    | Call next
    |--------------------------------------------------------------------------
    */

    public function callNext(Request $request): RedirectResponse
    {
        $tenant = $this->tenantContext->require();
        $user = $request->user();

        abort_unless($user->hasPermission('queues.update', $tenant), 403);

        $validated = $request->validate([
            'branch_id' => ['nullable', 'integer'],
            'doctor_id' => ['nullable', 'integer'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        try {
            $queue = $this->queueService->callNext(
                $tenant,
                $user,
                isset($validated['branch_id']) ? (int) $validated['branch_id'] : null,
                isset($validated['doctor_id']) ? (int) $validated['doctor_id'] : null,
                $validated['date'] ?? null
            );
        } catch (QueueException $exception) {
            return back()->withErrors(['queue' => $exception->getMessage()]);
        }

        if (! $queue) {
            return back()->withErrors([
                'queue' => 'There are no waiting patients in the queue.',
            ]);
        }

        $queue->load('patient');

        return back()->with(
            'success',
            "Now calling {$queue->ticket_number}"
            . ($queue->patient ? ' — ' . $this->patientName($queue) : '')
            . '.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ticket actions (call, recall, serve, complete, skip, no_show, cancel)
    |--------------------------------------------------------------------------
    */

    public function perform(
        Request $request,
        Queue $queue,
        string $action
    ): RedirectResponse {
        $tenant = $this->tenantContext->require();
        $user = $request->user();

        abort_unless($user->hasPermission('queues.update', $tenant), 403);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $updated = $this->queueService->perform(
                $tenant,
                $queue->id,
                $action,
                $user,
                $validated['reason'] ?? null
            );
        } catch (QueueException $exception) {
            return back()->withErrors(['queue' => $exception->getMessage()]);
        }

        return back()->with('success', $this->successMessage($updated, $action));
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function successMessage(Queue $queue, string $action): string
    {
        $ticket = $queue->ticket_number;

        return match ($action) {
            'call' => "Ticket {$ticket} has been called.",
            'recall' => "Ticket {$ticket} has been called again.",
            'serve' => "Ticket {$ticket} is now being served.",
            'complete' => "Ticket {$ticket} has been completed.",
            'skip' => "Ticket {$ticket} has been skipped.",
            'no_show' => "Ticket {$ticket} was marked as no-show.",
            'cancel' => "Ticket {$ticket} has been cancelled.",
            default => "Ticket {$ticket} has been updated.",
        };
    }

    protected function patientName(Queue $queue): string
    {
        return trim(
            ($queue->patient->first_name ?? '') . ' ' .
            ($queue->patient->last_name ?? '')
        );
    }

    /**
     * A valid Y-m-d date, or today in the clinic's timezone.
     */
    protected function resolveDate(mixed $value, Tenant $tenant): string
    {
        $today = Carbon::now(
            $tenant->timezone ?: config('app.timezone')
        )->toDateString();

        if (! is_string($value) || $value === '') {
            return $today;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $value);
        } catch (Throwable) {
            return $today;
        }

        return ($parsed && $parsed->format('Y-m-d') === $value)
            ? $value
            : $today;
    }
}