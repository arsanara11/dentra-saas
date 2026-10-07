<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetActiveTenant
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $tenantId = session('active_tenant_id');

        $tenant = $user->tenants()
            ->where('tenants.status', 'active')
            ->wherePivot('is_active', true)
            ->when(
                $tenantId,
                fn ($query) => $query->where('tenants.id', $tenantId)
            )
            ->first();

        if (! $tenant) {
            $tenant = $user->tenants()
                ->where('tenants.status', 'active')
                ->wherePivot('is_active', true)
                ->first();
        }

        if (! $tenant) {
            abort(403, 'You do not have access to an active clinic.');
        }

        session([
            'active_tenant_id' => $tenant->id,
        ]);

        $this->tenantContext->set($tenant);

        return $next($request);
    }
}