<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
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
            $request->user()->hasPermission('services.view', $tenant),
            403
        );

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        $base = Service::query()->where('tenant_id', $tenant->id);

        $statistics = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', Service::STATUS_ACTIVE)->count(),
            'inactive' => (clone $base)->where('status', Service::STATUS_INACTIVE)->count(),
        ];

        $services = (clone $base)
            ->withCount('appointments')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when(
                is_string($status) && in_array($status, Service::STATUSES, true),
                fn (Builder $query) => $query->where('status', $status)
            )
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('clinic.services.index', compact(
            'tenant',
            'services',
            'statistics',
            'search',
            'status'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Create / Store
    |--------------------------------------------------------------------------
    */

    public function create(Request $request): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('services.manage', $tenant),
            403
        );

        return view('clinic.services.create', [
            'tenant' => $tenant,
            'service' => new Service([
                'duration_minutes' => 30,
                'price' => 0,
                'status' => Service::STATUS_ACTIVE,
            ]),
            'categories' => $this->categories((int) $tenant->id),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('services.manage', $tenant),
            403
        );

        $validated = $this->validateService($request, (int) $tenant->id);

        $validated['tenant_id'] = $tenant->id;

        $service = Service::create($validated);

        return redirect()
            ->route('clinic.services.index')
            ->with('success', "Service \"{$service->name}\" has been created.");
    }

    /*
    |--------------------------------------------------------------------------
    | Edit / Update
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Service $service): View
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('services.manage', $tenant),
            403
        );

        $this->ensureBelongsToTenant($service, (int) $tenant->id);

        return view('clinic.services.edit', [
            'tenant' => $tenant,
            'service' => $service,
            'categories' => $this->categories((int) $tenant->id),
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('services.manage', $tenant),
            403
        );

        $this->ensureBelongsToTenant($service, (int) $tenant->id);

        $validated = $this->validateService(
            $request,
            (int) $tenant->id,
            (int) $service->id
        );

        $service->update($validated);

        return redirect()
            ->route('clinic.services.index')
            ->with('success', "Service \"{$service->name}\" has been updated.");
    }

    /*
    |--------------------------------------------------------------------------
    | Activate / deactivate
    |--------------------------------------------------------------------------
    */

    public function toggle(Request $request, Service $service): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('services.manage', $tenant),
            403
        );

        $this->ensureBelongsToTenant($service, (int) $tenant->id);

        $service->update([
            'status' => $service->isActive()
                ? Service::STATUS_INACTIVE
                : Service::STATUS_ACTIVE,
        ]);

        return back()->with(
            'success',
            $service->isActive()
                ? "Service \"{$service->name}\" is active again."
                : "Service \"{$service->name}\" has been deactivated."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, Service $service): RedirectResponse
    {
        $tenant = $this->tenantContext->require();

        abort_unless(
            $request->user()->hasPermission('services.manage', $tenant),
            403
        );

        $this->ensureBelongsToTenant($service, (int) $tenant->id);

        // A service that appears on appointments is kept for history.
        $usedBy = $service->appointments()->count();

        if ($usedBy > 0) {
            return back()->withErrors([
                'service' => "\"{$service->name}\" is used by {$usedBy} "
                    . ($usedBy === 1 ? 'appointment' : 'appointments')
                    . ' and cannot be deleted. Deactivate it instead.',
            ]);
        }

        $name = $service->name;

        $service->delete();

        return redirect()
            ->route('clinic.services.index')
            ->with('success', "Service \"{$name}\" has been deleted.");
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    private function validateService(
        Request $request,
        int $tenantId,
        ?int $ignoreId = null
    ): array {
        // Codes are stored upper case so "scaling" and "SCALING" collide.
        $request->merge([
            'code' => strtoupper(trim((string) $request->input('code'))),
        ]);

        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9][A-Z0-9_-]*$/',
                Rule::unique('services', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'status' => ['required', Rule::in(Service::STATUSES)],
        ], [
            'code.regex' => 'The code may only contain letters, numbers, dashes and underscores.',
            'code.unique' => 'This service code is already used in your clinic.',
        ]);
    }

    /**
     * Existing categories, offered as suggestions in the form.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function categories(int $tenantId)
    {
        return Service::query()
            ->where('tenant_id', $tenantId)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    private function ensureBelongsToTenant(Service $service, int $tenantId): void
    {
        abort_unless((int) $service->tenant_id === $tenantId, 404);
    }
}