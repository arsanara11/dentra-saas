<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    private function tenant(): Tenant
    {
        $tenant = app(TenantContext::class)->get();

        abort_unless(
            $tenant instanceof Tenant,
            403,
            'No active clinic.'
        );

        return $tenant;
    }

    private function authorizePermission(
        string $permission,
        Tenant $tenant
    ): void {
        abort_unless(
            auth()->user()->hasPermission($permission, $tenant),
            403,
            'You do not have permission to perform this action.'
        );
    }

    private function doctorQuery(Tenant $tenant): Builder
    {
        return Doctor::query()
            ->where('tenant_id', $tenant->id);
    }

    /**
     * Display the doctor directory.
     */
    public function index(Request $request)
    {
        $tenant = $this->tenant();

        $this->authorizePermission('doctors.view', $tenant);

        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $doctors = $this->doctorQuery($tenant)
            ->with('branch')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('doctor_code', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(
                in_array($status, ['active', 'inactive'], true),
                fn ($query) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => $this->doctorQuery($tenant)->count(),
            'active' => $this->doctorQuery($tenant)
                ->where('status', 'active')
                ->count(),
            'inactive' => $this->doctorQuery($tenant)
                ->where('status', 'inactive')
                ->count(),
        ];

        return view('clinic.doctors.index', compact(
            'tenant',
            'doctors',
            'summary',
            'search',
            'status'
        ));
    }

    /**
     * Show the form for creating a doctor.
     */
    public function create()
    {
        $tenant = $this->tenant();

        $this->authorizePermission('doctors.manage', $tenant);

        $branches = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        return view('clinic.doctors.create', compact(
            'tenant',
            'branches'
        ));
    }

    /**
     * Store a newly created doctor.
     */
    public function store(Request $request)
    {
        $tenant = $this->tenant();

        $this->authorizePermission('doctors.manage', $tenant);

        $validated = $request->validate([
            'doctor_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('doctors', 'doctor_code')
                    ->where('tenant_id', $tenant->id),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')
                    ->where('tenant_id', $tenant->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'specialization' => [
                'nullable',
                'string',
                'max:255',
            ],
            'license_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'education' => [
                'nullable',
                'string',
                'max:255',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        // Tenant ID is assigned from the active clinic,
        // never accepted from the submitted form.
        $validated['tenant_id'] = $tenant->id;

        Doctor::create($validated);

        return redirect()
            ->route('clinic.doctors.index')
            ->with('success', 'Doctor created successfully.');
    }

    /**
     * Display a doctor's details.
     */
    public function show(Doctor $doctor)
    {
        $tenant = $this->tenant();

        $this->authorizePermission('doctors.view', $tenant);

        abort_unless(
            (int) $doctor->tenant_id === (int) $tenant->id,
            404
        );

        $doctor->load('branch');

        return view('clinic.doctors.show', compact(
            'tenant',
            'doctor'
        ));
    }

    /**
     * Show the form for editing a doctor.
     */
    public function edit(Doctor $doctor)
    {
        $tenant = $this->tenant();

        $this->authorizePermission('doctors.manage', $tenant);

        abort_unless(
            (int) $doctor->tenant_id === (int) $tenant->id,
            404
        );

        $branches = Branch::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        return view('clinic.doctors.edit', compact(
            'tenant',
            'doctor',
            'branches'
        ));
    }

    /**
     * Update an existing doctor.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $tenant = $this->tenant();

        $this->authorizePermission('doctors.manage', $tenant);

        abort_unless(
            (int) $doctor->tenant_id === (int) $tenant->id,
            404
        );

        $validated = $request->validate([
            'doctor_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('doctors', 'doctor_code')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($doctor->id),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')
                    ->where('tenant_id', $tenant->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
            'specialization' => [
                'nullable',
                'string',
                'max:255',
            ],
            'license_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'education' => [
                'nullable',
                'string',
                'max:255',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $doctor->update($validated);

        return redirect()
            ->route('clinic.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    /**
     * Soft-delete a doctor.
     */
    public function destroy(Doctor $doctor)
    {
        $tenant = $this->tenant();

        $this->authorizePermission('doctors.manage', $tenant);

        abort_unless(
            (int) $doctor->tenant_id === (int) $tenant->id,
            404
        );

        $doctor->delete();

        return redirect()
            ->route('clinic.doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }
}