<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! $request->user()) {
            return $next($request);
        }

        $tenant = $request->user()
            ->tenants()
            ->where('tenants.status', 'active')
            ->wherePivot('is_active', true)
            ->first();

        if (! $tenant) {
            abort(403, 'You do not have access to an active clinic.');
        }

        $this->tenantContext->set($tenant);

        return $next($request);
    }
}