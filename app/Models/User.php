<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\TenantContext;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

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
     * Role and permissions already loaded during this request,
     * keyed by tenant id. See accessFor().
     *
     * @var array<int, array{role: Role|null, permissions: array<string, bool>}>
     */
    protected array $accessCache = [];

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
                'is_active',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Access control
    |--------------------------------------------------------------------------
    */

    /**
     * Load the user's role and permission slugs for one tenant.
     *
     * The result is kept on the model instance, so a request that checks
     * permissions many times (every @canDent, every controller) hits the
     * database three times in total instead of three times per check.
     *
     * @return array{role: Role|null, permissions: array<string, bool>}
     */
    protected function accessFor(?Tenant $tenant = null): array
    {
        $tenant ??= app(TenantContext::class)->get();

        if (! $tenant) {
            return ['role' => null, 'permissions' => []];
        }

        $tenantId = (int) $tenant->id;

        if (! array_key_exists($tenantId, $this->accessCache)) {
            $roleId = DB::table('tenant_user')
                ->where('tenant_id', $tenantId)
                ->where('user_id', $this->getKey())
                ->where('is_active', true)
                ->value('role_id');

            $role = $roleId ? Role::find($roleId) : null;

            $permissions = $role
                ? array_fill_keys(
                    $role->permissions()->pluck('permissions.slug')->all(),
                    true
                )
                : [];

            $this->accessCache[$tenantId] = [
                'role' => $role,
                'permissions' => $permissions,
            ];
        }

        return $this->accessCache[$tenantId];
    }

    /**
     * Forget the cached role and permissions, for example after
     * changing this user's role in the middle of a request.
     */
    public function flushAccessCache(): void
    {
        $this->accessCache = [];
    }

    public function roleForTenant(?Tenant $tenant = null): ?Role
    {
        return $this->accessFor($tenant)['role'];
    }

    public function hasPermission(
        string $permission,
        ?Tenant $tenant = null
    ): bool {
        return isset($this->accessFor($tenant)['permissions'][$permission]);
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