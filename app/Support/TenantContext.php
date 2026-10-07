<?php

namespace App\Support;

use App\Models\Tenant;

class TenantContext
{
    protected ?Tenant $tenant = null;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }

    public function check(): bool
    {
        return $this->tenant !== null;
    }

    public function require(): Tenant
    {
        if (! $this->tenant) {
            abort(403, 'No active tenant selected.');
        }

        return $this->tenant;
    }
}