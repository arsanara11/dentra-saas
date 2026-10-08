<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Tenant;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')
            ->using(TenantUser::class)
            ->withPivot([
                'role_id',
                'role',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function roleForTenant(?Tenant $tenant = null): ?Role
    {
        $tenant ??= app(\App\Support\TenantContext::class)->get();

        if (! $tenant) {
            return null;
        }

        $membership = $this->tenants()
            ->where('tenants.id', $tenant->id)
            ->wherePivot('is_active', true)
            ->first();

        if (! $membership || ! $membership->pivot->role_id) {
            return null;
        }

        return Role::find($membership->pivot->role_id);
    }

    public function hasPermission(
        string $permission,
        ?Tenant $tenant = null
    ): bool {
        $role = $this->roleForTenant($tenant);

        if (! $role) {
            return false;
        }

        return $role->permissions()
            ->where('permissions.slug', $permission)
            ->exists();
    }

    public function hasAnyPermission(
        array $permissions,
        ?Tenant $tenant = null
    ): bool {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $tenant)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(
        array $permissions,
        ?Tenant $tenant = null
    ): bool {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission, $tenant)) {
                return false;
            }
        }

        return true;
    }

    public function isRole(
        string|array $roles,
        ?Tenant $tenant = null
    ): bool {
        $role = $this->roleForTenant($tenant);

        if (! $role) {
            return false;
        }

        $roles = is_array($roles)
            ? $roles
            : [$roles];

        return in_array($role->slug, $roles, true);
    }
}
