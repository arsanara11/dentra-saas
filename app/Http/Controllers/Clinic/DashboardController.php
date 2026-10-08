<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    public function index(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('dashboard.view', $tenant),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Patient Statistics
        |--------------------------------------------------------------------------
        */

        $totalPatients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->count();

        $activePatients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        $newPatients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->whereDate('created_at', today())
            ->count();

        $recentPatients = Patient::query()
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->take(4)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Demo Dashboard Data
        |--------------------------------------------------------------------------
        |
        | Appointment, queue, consultation, and clinical modules
        | will be connected when their respective modules are built.
        |
        */

        $returningPatients = max(
            0,
            $activePatients - $newPatients
        );

        $todayVisits = $newPatients + $returningPatients;

        /*
        |--------------------------------------------------------------------------
        | Temporary Schedule
        |--------------------------------------------------------------------------
        */

        $schedule = [
            [
                'time' => '08:00 AM',
                'title' => 'Morning Consultation',
                'doctor' => 'Dr. Sarah',
            ],
            [
                'time' => '10:00 AM',
                'title' => 'Dental Checkup',
                'doctor' => 'Dr. Michael',
            ],
            [
                'time' => '01:00 PM',
                'title' => 'Treatment Session',
                'doctor' => 'Dr. Sarah',
            ],
            [
                'time' => '03:30 PM',
                'title' => 'Follow-up Visit',
                'doctor' => 'Dr. Michael',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Queue
        |--------------------------------------------------------------------------
        */

        $queue = [
            [
                'number' => 'A-01',
                'name' => 'Guy Hawkins',
                'type' => 'Weekly Visit',
                'time' => '08:00 AM',
            ],
            [
                'number' => 'A-02',
                'name' => 'Jane Cooper',
                'type' => 'Weekly Visit',
                'time' => '10:00 AM',
            ],
            [
                'number' => 'A-03',
                'name' => 'Leslie Alexander',
                'type' => 'Dental Checkup',
                'time' => '02:00 PM',
            ],
            [
                'number' => 'A-04',
                'name' => 'Jenny Wilson',
                'type' => 'Routine Checkup',
                'time' => '04:00 PM',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Dentist Notes
        |--------------------------------------------------------------------------
        */

        $notes = [
            [
                'title' => 'Review today\'s patient records',
                'time' => 'Today',
                'type' => 'Clinical',
            ],
            [
                'title' => 'Follow up with patients',
                'time' => 'Today',
                'type' => 'Follow-up',
            ],
            [
                'title' => 'Check treatment schedule',
                'time' => 'Tomorrow',
                'type' => 'Schedule',
            ],
        ];

        return view('clinic.dashboard', [
            'tenant' => $tenant,
            'totalPatients' => $totalPatients,
            'activePatients' => $activePatients,
            'newPatients' => $newPatients,
            'returningPatients' => $returningPatients,
            'todayVisits' => $todayVisits,
            'recentPatients' => $recentPatients,
            'schedule' => $schedule,
            'queue' => $queue,
            'notes' => $notes,
        ]);
    }
}