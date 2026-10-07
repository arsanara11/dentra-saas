<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'timezone',
        'currency',
        'status',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->using(TenantUser::class)
            ->withPivot([
                'role_id',
                'role',
                'is_active',
            ])
            ->withTimestamps();
    }
}
