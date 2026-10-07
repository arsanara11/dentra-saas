<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Support\TenantContext;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    public function index(): View
    {
        $tenant = $this->tenantContext->require();

        $branches = $tenant->branches()
            ->where('status', 'active')
            ->get();

        return view('clinic.dashboard', [
            'tenant' => $tenant,
            'branches' => $branches,
        ]);
    }
}