<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Tenant;
use App\Support\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    public function index(Request $request): View
    {
        $tenant = $this->tenantContext->require();
        $user = $request->user();

        abort_unless($user->hasPermission('dashboard.view', $tenant), 403);

        // The dashboard only shows data from modules the user may open.
        $canViewQueue = $user->hasPermission('queues.view', $tenant);
        $canViewAppointments = $user->hasPermission('appointments.view', $tenant);

        // Everything is calculated in the clinic's own timezone.
        $timezone = $tenant->timezone ?: config('app.timezone');
        $now = Carbon::now($timezone);
        $todayDate = $now->toDateString();

        $overview = $canViewQueue
            ? $this->queueOverview($tenant, $now, $timezone)
            : $this->emptyQueueOverview();

        $upcoming = $canViewAppointments
            ? $this->upcomingAppointments($tenant, $now)
            : [];

        $calendar = $this->calendar(
            $request,
            $tenant,
            $now,
            $timezone,
            $canViewAppointments
        );

        $reminders = $this->reminders(
            $tenant,
            $todayDate,
            $overview['summary'],
            $canViewQueue,
            $canViewAppointments
        );

        return view('clinic.dashboard', [
            'tenant' => $tenant,
            'today' => $now,
            'greeting' => $this->greeting($now),
            'canViewQueue' => $canViewQueue,
            'canViewAppointments' => $canViewAppointments,

            // Hero
            'todayVisits' => $overview['visits'],
            'newPatients' => $overview['new'],
            'returningPatients' => $overview['returning'],

            // Queue
            'queueToday' => $overview['list'],
            'queueSummary' => $overview['summary'],
            'nowServing' => $overview['nowServing'],

            // Right column
            'calendar' => $calendar,
            'upcoming' => $upcoming,
            'reminders' => $reminders,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    protected function queueOverview(
        Tenant $tenant,
        Carbon $now,
        string $timezone
    ): array {
        $base = Queue::query()
            ->where('tenant_id', $tenant->id)
            ->whereDate('queue_date', $now->toDateString());

        $counts = (clone $base)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // A "visit" is a patient who arrived today and has a ticket that
        // was not cancelled. A patient with two tickets counts once.
        $visitingPatientIds = (clone $base)
            ->where('status', '!=', Queue::STATUS_CANCELLED)
            ->distinct()
            ->pluck('patient_id');

        $visits = $visitingPatientIds->count();

        // "New" = registered today. The rest are returning patients.
        $startOfDay = $now->copy()->startOfDay()->setTimezone(config('app.timezone'));
        $endOfDay = $now->copy()->endOfDay()->setTimezone(config('app.timezone'));

        $new = $visits === 0
            ? 0
            : Patient::query()
                ->where('tenant_id', $tenant->id)
                ->whereIn('id', $visitingPatientIds)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count();

        $list = (clone $base)
            ->with('patient')
            ->orderByRaw("
                CASE status
                    WHEN 'serving' THEN 0
                    WHEN 'called' THEN 1
                    WHEN 'waiting' THEN 2
                    WHEN 'skipped' THEN 3
                    WHEN 'completed' THEN 4
                    WHEN 'no_show' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('prefix')
            ->orderBy('queue_number')
            ->limit(5)
            ->get();

        $nowServing = (clone $base)
            ->with('patient')
            ->where('status', Queue::STATUS_SERVING)
            ->orderBy('started_at')
            ->first();

        return [
            'visits' => $visits,
            'new' => $new,
            'returning' => max(0, $visits - $new),
            'list' => $list,
            'nowServing' => $nowServing,
            'summary' => [
                'waiting' => (int) ($counts[Queue::STATUS_WAITING] ?? 0),
                'called' => (int) ($counts[Queue::STATUS_CALLED] ?? 0),
                'serving' => (int) ($counts[Queue::STATUS_SERVING] ?? 0),
                'completed' => (int) ($counts[Queue::STATUS_COMPLETED] ?? 0),
                'skipped' => (int) ($counts[Queue::STATUS_SKIPPED] ?? 0),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function emptyQueueOverview(): array
    {
        return [
            'visits' => 0,
            'new' => 0,
            'returning' => 0,
            'list' => collect(),
            'nowServing' => null,
            'summary' => [
                'waiting' => 0,
                'called' => 0,
                'serving' => 0,
                'completed' => 0,
                'skipped' => 0,
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Appointments
    |--------------------------------------------------------------------------
    */

    /**
     * The next appointments that have not started yet.
     *
     * @return list<array{title: string, meta: string, url: string}>
     */
    protected function upcomingAppointments(Tenant $tenant, Carbon $now): array
    {
        $todayDate = $now->toDateString();
        $nowTime = $now->format('H:i:s');

        $appointments = Appointment::query()
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [
                Appointment::STATUS_SCHEDULED,
                Appointment::STATUS_CONFIRMED,
            ])
            ->where(function ($query) use ($todayDate, $nowTime) {
                $query->whereDate('appointment_date', '>', $todayDate)
                    ->orWhere(function ($query) use ($todayDate, $nowTime) {
                        $query->whereDate('appointment_date', $todayDate)
                            ->where('start_time', '>=', $nowTime);
                    });
            })
            ->with('patient')
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(4)
            ->get();

        return $appointments->map(function (Appointment $appointment) use ($todayDate) {
            $isToday = $appointment->appointment_date->toDateString() === $todayDate;

            $patientName = trim(
                ($appointment->patient->first_name ?? '') . ' ' .
                ($appointment->patient->last_name ?? '')
            );

            return [
                'title' => $appointment->reason ?: 'Appointment',
                'meta' => ($isToday ? 'Today' : $appointment->appointment_date->format('d M'))
                    . ', ' . Carbon::parse($appointment->start_time)->format('h:i A')
                    . ($patientName !== '' ? ' · ' . $patientName : ''),
                'url' => route('clinic.appointments.show', $appointment),
            ];
        })->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    */

    /**
     * Month grid data. The month can be changed with ?month=YYYY-MM.
     *
     * @return array<string, mixed>
     */
    protected function calendar(
        Request $request,
        Tenant $tenant,
        Carbon $now,
        string $timezone,
        bool $canViewAppointments
    ): array {
        $month = $now->copy()->startOfMonth();
        $requested = $request->input('month');

        if (is_string($requested) && preg_match('/^\d{4}-\d{2}$/', $requested)) {
            try {
                $parsed = Carbon::createFromFormat('!Y-m', $requested, $timezone);

                if ($parsed && $parsed->format('Y-m') === $requested) {
                    $month = $parsed;
                }
            } catch (Throwable) {
                // Fall back to the current month.
            }
        }

        // Days of this month that have at least one appointment.
        $busyDays = [];

        if ($canViewAppointments) {
            $busyDays = Appointment::query()
                ->where('tenant_id', $tenant->id)
                ->whereBetween('appointment_date', [
                    $month->copy()->startOfMonth()->toDateString(),
                    $month->copy()->endOfMonth()->toDateString(),
                ])
                ->whereNotIn('status', [
                    Appointment::STATUS_CANCELLED,
                    Appointment::STATUS_NO_SHOW,
                ])
                ->get(['appointment_date'])
                ->countBy(fn ($appointment) => (int) $appointment->appointment_date->format('j'))
                ->all();
        }

        return [
            'month' => $month->format('Y-m'),
            'label' => $month->format('F Y'),
            'prev' => $month->copy()->subMonthNoOverflow()->format('Y-m'),
            'next' => $month->copy()->addMonthNoOverflow()->format('Y-m'),
            'firstDay' => $month->dayOfWeek,
            'daysInMonth' => $month->daysInMonth,
            'todayDay' => $month->format('Y-m') === $now->format('Y-m') ? $now->day : null,
            'busy' => $busyDays,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Reminders
    |--------------------------------------------------------------------------
    */

    /**
     * Things that need attention right now, based on live data.
     *
     * @param  array<string, int>  $summary
     * @return list<array{title: string, meta: string, url: string}>
     */
    protected function reminders(
        Tenant $tenant,
        string $todayDate,
        array $summary,
        bool $canViewQueue,
        bool $canViewAppointments
    ): array {
        $reminders = [];

        if ($canViewQueue) {
            if ($summary['called'] > 0) {
                $reminders[] = [
                    'title' => $summary['called'] . ' called '
                        . Str::plural('patient', $summary['called'])
                        . ' awaiting response',
                    'meta' => 'Queue',
                    'url' => route('clinic.queues.index', ['date' => $todayDate, 'status' => 'called']),
                ];
            }

            if ($summary['waiting'] > 0) {
                $reminders[] = [
                    'title' => $summary['waiting'] . ' '
                        . Str::plural('patient', $summary['waiting'])
                        . ' waiting in the queue',
                    'meta' => 'Queue',
                    'url' => route('clinic.queues.index', ['date' => $todayDate, 'status' => 'waiting']),
                ];
            }

            if ($summary['skipped'] > 0) {
                $reminders[] = [
                    'title' => $summary['skipped'] . ' skipped '
                        . Str::plural('ticket', $summary['skipped'])
                        . ' to recall or close',
                    'meta' => 'Queue',
                    'url' => route('clinic.queues.index', ['date' => $todayDate, 'status' => 'skipped']),
                ];
            }
        }

        if ($canViewAppointments) {
            $notCheckedIn = Appointment::query()
                ->where('tenant_id', $tenant->id)
                ->whereDate('appointment_date', $todayDate)
                ->whereIn('status', [
                    Appointment::STATUS_SCHEDULED,
                    Appointment::STATUS_CONFIRMED,
                ])
                ->count();

            if ($notCheckedIn > 0) {
                $reminders[] = [
                    'title' => $notCheckedIn . ' '
                        . Str::plural('appointment', $notCheckedIn)
                        . ' today not checked in yet',
                    'meta' => 'Appointments',
                    'url' => route('clinic.appointments.index', ['date' => $todayDate]),
                ];
            }
        }

        return $reminders;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function greeting(Carbon $now): string
    {
        return match (true) {
            $now->hour < 12 => 'Good Morning',
            $now->hour < 17 => 'Good Afternoon',
            default => 'Good Evening',
        };
    }
}