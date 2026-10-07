<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions'
        )->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'tenant_user',
            'role_id',
            'user_id'
        )->withPivot([
            'tenant_id',
            'is_active',
        ])->withTimestamps();
    }
}