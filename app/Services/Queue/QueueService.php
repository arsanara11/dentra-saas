<?php

namespace App\Services\Queue;

use App\Exceptions\QueueException;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\QueueEvent;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Single place for every queue business rule:
 *
 *  - safe ticket number generation (row lock + retry)
 *  - ticket state machine
 *  - audit trail (queue_events)
 *
 * The tenant is always passed in explicitly from the authenticated
 * context. Nothing in here trusts a tenant_id coming from a request.
 */
class QueueService
{
    /**
     * How many times to retry when two requests collide on the
     * unique ticket number index.
     */
    protected const MAX_ATTEMPTS = 3;

    /**
     * Ticket state machine.
     *
     * action => allowed current statuses, resulting status, audit event.
     *
     *   waiting ─call──▶ called ─serve──▶ serving ─complete──▶ completed
     *      │               │  ▲
     *      │               │  └──recall──┐
     *      │               ├─skip──▶ skipped
     *      │               └─no_show─▶ no_show
     *      └─cancel──▶ cancelled (from waiting / called / skipped)
     */
    public const ACTIONS = [
        'call' => [
            'from' => [Queue::STATUS_WAITING],
            'to' => Queue::STATUS_CALLED,
            'event' => QueueEvent::TYPE_CALLED,
        ],
        'recall' => [
            'from' => [Queue::STATUS_CALLED, Queue::STATUS_SKIPPED],
            'to' => Queue::STATUS_CALLED,
            'event' => QueueEvent::TYPE_RECALLED,
        ],
        'serve' => [
            'from' => [Queue::STATUS_CALLED],
            'to' => Queue::STATUS_SERVING,
            'event' => QueueEvent::TYPE_SERVING,
        ],
        'complete' => [
            'from' => [Queue::STATUS_SERVING],
            'to' => Queue::STATUS_COMPLETED,
            'event' => QueueEvent::TYPE_COMPLETED,
        ],
        'skip' => [
            'from' => [Queue::STATUS_CALLED],
            'to' => Queue::STATUS_SKIPPED,
            'event' => QueueEvent::TYPE_SKIPPED,
        ],
        'no_show' => [
            'from' => [Queue::STATUS_CALLED, Queue::STATUS_SKIPPED],
            'to' => Queue::STATUS_NO_SHOW,
            'event' => QueueEvent::TYPE_NO_SHOW,
        ],
        'cancel' => [
            'from' => [
                Queue::STATUS_WAITING,
                Queue::STATUS_CALLED,
                Queue::STATUS_SKIPPED,
            ],
            'to' => Queue::STATUS_CANCELLED,
            'event' => QueueEvent::TYPE_CANCELLED,
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Public API
    |--------------------------------------------------------------------------
    */

    /**
     * Actions that may be performed on a ticket with the given status.
     * Used by views to decide which buttons to render.
     *
     * @return list<string>
     */
    public function availableActions(string $status): array
    {
        $actions = [];

        foreach (self::ACTIONS as $action => $definition) {
            if (in_array($status, $definition['from'], true)) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    /**
     * Add a patient to the queue.
     *
     * Accepted keys in $data:
     *   patient_id (required), branch_id, doctor_id, appointment_id,
     *   queue_date (Y-m-d, defaults to today), priority, prefix
     *
     * @param  array<string, mixed>  $data
     *
     * @throws QueueException
     */
    public function enqueue(Tenant $tenant, User $actor, array $data): Queue
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                return DB::transaction(
                    fn () => $this->createTicket($tenant, $actor, $data)
                );
            } catch (UniqueConstraintViolationException $exception) {
                // Another request took the same number at the same moment.
                if ($attempt >= self::MAX_ATTEMPTS) {
                    report($exception);

                    throw new QueueException(
                        'The queue number could not be generated. Please try again.'
                    );
                }

                usleep(random_int(20_000, 80_000));
            }
        }
    }

    /**
     * Check a patient in for their appointment.
     *
     * Issues a queue ticket linked to the appointment and moves the
     * appointment to "checked_in" in the same transaction. Only possible
     * on the day of the appointment.
     *
     * Accepted keys in $options: doctor_id, priority
     *
     * @param  array<string, mixed>  $options
     *
     * @throws QueueException
     */
    public function checkInAppointment(
        Tenant $tenant,
        User $actor,
        int $appointmentId,
        array $options = []
    ): Queue {
        $appointment = Appointment::query()
            ->where('tenant_id', $tenant->id)
            ->whereKey($appointmentId)
            ->first();

        if (! $appointment) {
            throw new QueueException('The selected appointment was not found.');
        }

        $today = $this->today($tenant);

        if ($appointment->appointment_date->toDateString() !== $today) {
            throw new QueueException('Check-in is only available on the day of the appointment.');
        }

        return $this->enqueue($tenant, $actor, [
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'branch_id' => $appointment->branch_id,
            'doctor_id' => $options['doctor_id'] ?? null,
            'priority' => $options['priority'] ?? Queue::PRIORITY_NORMAL,
            'queue_date' => $today,
        ]);
    }

    /**
     * Call the next waiting ticket.
     *
     * Order: emergency, then priority, then normal; first come first
     * served inside the same priority. Returns null when nobody waits.
     *
     * @throws QueueException
     */
    public function callNext(
        Tenant $tenant,
        User $actor,
        ?int $branchId = null,
        ?int $doctorId = null,
        ?string $date = null
    ): ?Queue {
        $date ??= $this->today($tenant);

        return DB::transaction(function () use (
            $tenant,
            $actor,
            $branchId,
            $doctorId,
            $date
        ) {
            $next = Queue::query()
                ->where('tenant_id', $tenant->id)
                ->whereDate('queue_date', $date)
                ->where('status', Queue::STATUS_WAITING)
                ->when(
                    $branchId,
                    fn ($query) => $query->where('branch_id', $branchId)
                )
                ->when(
                    $doctorId,
                    fn ($query) => $query->where(
                        fn ($query) => $query
                            ->where('doctor_id', $doctorId)
                            ->orWhereNull('doctor_id')
                    )
                )
                ->orderByRaw(
                    "CASE priority
                        WHEN 'emergency' THEN 0
                        WHEN 'priority' THEN 1
                        ELSE 2
                    END"
                )
                ->orderBy('queue_number')
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $next) {
                return null;
            }

            return $this->applyAction($next, 'call', $actor, null);
        });
    }

    /**
     * Run a state machine action on one ticket.
     *
     * @param  Queue|int  $queue  Ticket or ticket id
     *
     * @throws QueueException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException When the
     *         ticket does not belong to the tenant (renders as 404).
     */
    public function perform(
        Tenant $tenant,
        Queue|int $queue,
        string $action,
        User $actor,
        ?string $reason = null
    ): Queue {
        if (! isset(self::ACTIONS[$action])) {
            throw new QueueException('Unknown queue action.');
        }

        $queueId = $queue instanceof Queue ? $queue->id : $queue;

        return DB::transaction(function () use (
            $tenant,
            $queueId,
            $action,
            $actor,
            $reason
        ) {
            // Re-read the ticket under a row lock so two staff members
            // clicking at the same moment cannot both win.
            $locked = Queue::query()
                ->where('tenant_id', $tenant->id)
                ->whereKey($queueId)
                ->lockForUpdate()
                ->firstOrFail();

            return $this->applyAction($locked, $action, $actor, $reason);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Ticket creation
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createTicket(
        Tenant $tenant,
        User $actor,
        array $data
    ): Queue {
        $today = $this->today($tenant);
        $queueDate = $this->resolveDate($data['queue_date'] ?? null, $today);
        $priority = $data['priority'] ?? Queue::PRIORITY_NORMAL;

        if (! in_array($priority, Queue::PRIORITIES, true)) {
            throw new QueueException('Invalid queue priority.');
        }

        // Patient (locked so the same patient cannot be queued twice
        // by two simultaneous requests).
        $patient = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->whereKey($data['patient_id'] ?? 0)
            ->lockForUpdate()
            ->first();

        if (! $patient) {
            throw new QueueException('The selected patient was not found or is inactive.');
        }

        // Appointment (optional)
        $appointment = null;

        if (! empty($data['appointment_id'])) {
            $appointment = $this->resolveAppointment(
                $tenant,
                $patient,
                (int) $data['appointment_id'],
                $queueDate
            );
        }

        // Branch: explicit > appointment branch > main branch
        $branch = $this->resolveBranch(
            $tenant,
            $data['branch_id'] ?? $appointment?->branch_id
        );

        // Doctor (optional)
        $doctor = null;

        if (! empty($data['doctor_id'])) {
            $doctor = Doctor::query()
                ->where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->whereKey($data['doctor_id'])
                ->first();

            if (! $doctor) {
                throw new QueueException('The selected doctor was not found or is inactive.');
            }

            if ($doctor->branch_id && (int) $doctor->branch_id !== (int) $branch->id) {
                throw new QueueException('The selected doctor is not assigned to this branch.');
            }
        }

        // One active ticket per patient per branch per day.
        $alreadyWaiting = Queue::query()
            ->where('tenant_id', $tenant->id)
            ->where('branch_id', $branch->id)
            ->whereDate('queue_date', $queueDate)
            ->where('patient_id', $patient->id)
            ->whereIn('status', Queue::ACTIVE_STATUSES)
            ->exists();

        if ($alreadyWaiting) {
            throw new QueueException('This patient already has an active queue ticket for that day.');
        }

        // Next number. The branch row is locked first, which serialises
        // number generation per branch (MAX() alone cannot lock an empty
        // result set). Soft-deleted rows are included because the unique
        // index still counts them.
        $prefix = $this->normalisePrefix($data['prefix'] ?? null);
        $appointmentStatusBefore = $appointment?->status;

        $lastNumber = Queue::withTrashed()
            ->where('tenant_id', $tenant->id)
            ->where('branch_id', $branch->id)
            ->whereDate('queue_date', $queueDate)
            ->where('prefix', $prefix)
            ->max('queue_number');

        $queue = Queue::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'doctor_id' => $doctor?->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment?->id,
            'queue_date' => $queueDate,
            'prefix' => $prefix,
            'queue_number' => ((int) $lastNumber) + 1,
            'priority' => $priority,
            'status' => Queue::STATUS_WAITING,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        // A ticket linked to an appointment means the patient has arrived.
        if ($appointment) {
            $appointment->update(['status' => Appointment::STATUS_CHECKED_IN]);
        }

        $this->recordEvent($queue, QueueEvent::TYPE_CREATED, null, $queue->status, $actor, [
            'ticket_number' => $queue->ticket_number,
            'priority' => $priority,
            'source' => $appointment ? 'appointment' : 'walk_in',
            'appointment_id' => $appointment?->id,
            'appointment_status_before' => $appointmentStatusBefore,
            'doctor_id' => $doctor?->id,
        ]);

        return $queue;
    }

    /*
    |--------------------------------------------------------------------------
    | State machine
    |--------------------------------------------------------------------------
    */

    /**
     * Apply an action to a ticket that is already row-locked
     * inside a transaction.
     *
     * @throws QueueException
     */
    protected function applyAction(
        Queue $queue,
        string $action,
        User $actor,
        ?string $reason
    ): Queue {
        $definition = self::ACTIONS[$action];

        if (! in_array($queue->status, $definition['from'], true)) {
            throw new QueueException(sprintf(
                'Ticket %s is "%s" and cannot be changed with "%s".',
                $queue->ticket_number,
                str_replace('_', ' ', $queue->status),
                str_replace('_', ' ', $action)
            ));
        }

        if ($action === 'serve') {
            $this->ensureDoctorIsFree($queue);
        }

        $from = $queue->status;
        $to = $definition['to'];
        $now = now();

        $attributes = [
            'status' => $to,
            'updated_by' => $actor->id,
        ];

        if ($action === 'call' || $action === 'recall') {
            $attributes['called_at'] = $now;
            $attributes['skipped_at'] = null;
        }

        if ($action === 'serve') {
            $attributes['started_at'] = $now;
        }

        if ($action === 'complete') {
            $attributes['completed_at'] = $now;
        }

        if ($action === 'skip') {
            $attributes['skipped_at'] = $now;
        }

        $queue->update($attributes);

        $appointmentStatus = $this->syncAppointment($queue, $action);

        $metadata = [
            'action' => $action,
            'ticket_number' => $queue->ticket_number,
        ];

        if ($appointmentStatus !== null) {
            $metadata['appointment_status'] = $appointmentStatus;
        }

        $reason = $this->normaliseReason($reason);

        if ($reason !== null) {
            $metadata['reason'] = $reason;
        }

        if ($action === 'recall') {
            $metadata['recall_number'] = QueueEvent::query()
                ->where('tenant_id', $queue->tenant_id)
                ->where('queue_id', $queue->id)
                ->where('event_type', QueueEvent::TYPE_RECALLED)
                ->count() + 1;
        }

        $this->recordEvent(
            $queue,
            $definition['event'],
            $from,
            $to,
            $actor,
            $metadata
        );

        return $queue;
    }

    /**
     * Keep the linked appointment in step with its ticket.
     *
     *   complete → appointment "completed"
     *   no_show  → appointment "no_show"
     *   cancel   → check-in is undone: back to the status it had before
     *
     * Only an appointment that is still "checked_in" is touched, so a
     * manual change made by staff is never overwritten.
     *
     * @return string|null The new appointment status, or null if unchanged.
     */
    protected function syncAppointment(Queue $queue, string $action): ?string
    {
        if (! $queue->appointment_id) {
            return null;
        }

        $newStatus = match ($action) {
            'complete' => Appointment::STATUS_COMPLETED,
            'no_show' => Appointment::STATUS_NO_SHOW,
            'cancel' => $this->statusBeforeCheckIn($queue),
            default => null,
        };

        if ($newStatus === null) {
            return null;
        }

        $appointment = Appointment::query()
            ->where('tenant_id', $queue->tenant_id)
            ->whereKey($queue->appointment_id)
            ->lockForUpdate()
            ->first();

        if (! $appointment || $appointment->status !== Appointment::STATUS_CHECKED_IN) {
            return null;
        }

        $appointment->update(['status' => $newStatus]);

        return $newStatus;
    }

    /**
     * The appointment status recorded when the ticket was created.
     */
    protected function statusBeforeCheckIn(Queue $queue): string
    {
        $created = QueueEvent::query()
            ->where('tenant_id', $queue->tenant_id)
            ->where('queue_id', $queue->id)
            ->where('event_type', QueueEvent::TYPE_CREATED)
            ->first();

        $before = $created?->metadata['appointment_status_before'] ?? null;

        return in_array($before, [
            Appointment::STATUS_SCHEDULED,
            Appointment::STATUS_CONFIRMED,
        ], true)
            ? $before
            : Appointment::STATUS_SCHEDULED;
    }

    /**
     * A doctor can only serve one patient at a time.
     *
     * @throws QueueException
     */
    protected function ensureDoctorIsFree(Queue $queue): void
    {
        if (! $queue->doctor_id) {
            return;
        }

        // Lock the doctor row so two tickets for the same doctor cannot
        // both move to "serving" at the same time.
        Doctor::query()
            ->where('tenant_id', $queue->tenant_id)
            ->whereKey($queue->doctor_id)
            ->lockForUpdate()
            ->first();

        $busy = Queue::query()
            ->where('tenant_id', $queue->tenant_id)
            ->where('doctor_id', $queue->doctor_id)
            ->whereDate('queue_date', $queue->queue_date)
            ->where('status', Queue::STATUS_SERVING)
            ->whereKeyNot($queue->id)
            ->exists();

        if ($busy) {
            throw new QueueException(
                'This doctor is already serving another patient. Complete that ticket first.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Audit
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function recordEvent(
        Queue $queue,
        string $eventType,
        ?string $from,
        ?string $to,
        User $actor,
        array $metadata = []
    ): QueueEvent {
        return QueueEvent::create([
            'tenant_id' => $queue->tenant_id,
            'queue_id' => $queue->id,
            'event_type' => $eventType,
            'from_status' => $from,
            'to_status' => $to,
            'actor_id' => $actor->id,
            'metadata' => $metadata ?: null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Resolvers and helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Today's date in the clinic's own timezone.
     */
    protected function today(Tenant $tenant): string
    {
        return Carbon::now(
            $tenant->timezone ?: config('app.timezone')
        )->toDateString();
    }

    /**
     * @throws QueueException
     */
    protected function resolveDate(?string $date, string $today): string
    {
        if ($date === null || $date === '') {
            return $today;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $date);
        } catch (Throwable) {
            throw new QueueException('The queue date is not valid.');
        }

        if (! $parsed || $parsed->format('Y-m-d') !== $date) {
            throw new QueueException('The queue date is not valid.');
        }

        if ($date < $today) {
            throw new QueueException('A patient cannot be queued for a past date.');
        }

        return $date;
    }

    /**
     * @throws QueueException
     */
    protected function resolveAppointment(
        Tenant $tenant,
        Patient $patient,
        int $appointmentId,
        string $queueDate
    ): Appointment {
        $appointment = Appointment::query()
            ->where('tenant_id', $tenant->id)
            ->whereKey($appointmentId)
            ->lockForUpdate()
            ->first();

        if (! $appointment) {
            throw new QueueException('The selected appointment was not found.');
        }

        if ((int) $appointment->patient_id !== (int) $patient->id) {
            throw new QueueException('The selected appointment does not belong to this patient.');
        }

        if ($appointment->status === Appointment::STATUS_CHECKED_IN) {
            throw new QueueException('This appointment is already checked in.');
        }

        if (! in_array($appointment->status, [
            Appointment::STATUS_SCHEDULED,
            Appointment::STATUS_CONFIRMED,
        ], true)) {
            throw new QueueException(sprintf(
                'A %s appointment cannot be checked in.',
                str_replace('_', ' ', $appointment->status)
            ));
        }

        if ($appointment->appointment_date->toDateString() !== $queueDate) {
            throw new QueueException('The appointment is scheduled for a different date.');
        }

        $hasTicket = Queue::query()
            ->where('tenant_id', $tenant->id)
            ->where('appointment_id', $appointment->id)
            ->whereNotIn('status', [
                Queue::STATUS_CANCELLED,
                Queue::STATUS_NO_SHOW,
            ])
            ->exists();

        if ($hasTicket) {
            throw new QueueException('This appointment already has a queue ticket.');
        }

        return $appointment;
    }

    /**
     * Resolve and lock the branch the ticket belongs to.
     *
     * @throws QueueException
     */
    protected function resolveBranch(Tenant $tenant, mixed $branchId): Branch
    {
        $query = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->lockForUpdate();

        $branch = $branchId
            ? $query->whereKey($branchId)->first()
            : $query->orderByDesc('is_main')->orderBy('id')->first();

        if (! $branch) {
            throw new QueueException(
                $branchId
                    ? 'The selected branch was not found.'
                    : 'This clinic has no branch yet. Create a branch first.'
            );
        }

        return $branch;
    }

    protected function normalisePrefix(?string $prefix): string
    {
        $clean = strtoupper(
            substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $prefix), 0, 5)
        );

        return $clean !== '' ? $clean : 'A';
    }

    protected function normaliseReason(?string $reason): ?string
    {
        $reason = trim((string) $reason);

        if ($reason === '') {
            return null;
        }

        return mb_substr($reason, 0, 255);
    }
}