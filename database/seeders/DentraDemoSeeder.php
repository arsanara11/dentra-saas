<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DentraDemoSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Demo Tenant
        |--------------------------------------------------------------------------
        */

        $tenant = Tenant::updateOrCreate(
            [
                'slug' => 'smile-dental-clinic',
            ],
            [
                'name' => 'Smile Dental Clinic',
                'email' => 'hello@smiledental.test',
                'phone' => '0251-123456',
                'address' => 'Jl. Pajajaran No. 10',
                'city' => 'Bogor',
                'province' => 'Jawa Barat',
                'timezone' => 'Asia/Jakarta',
                'currency' => 'IDR',
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Demo Branch
        |--------------------------------------------------------------------------
        */

        Branch::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'code' => 'BOG-01',
            ],
            [
                'name' => 'Bogor Main Branch',
                'email' => 'bogor@smiledental.test',
                'phone' => '0251-123456',
                'address' => 'Jl. Pajajaran No. 10',
                'city' => 'Bogor',
                'province' => 'Jawa Barat',
                'postal_code' => '16128',
                'timezone' => 'Asia/Jakarta',
                'is_main' => true,
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Demo User
        |--------------------------------------------------------------------------
        */

        $user = User::updateOrCreate(
            [
                'email' => 'admin@dentra.test',
            ],
            [
                'name' => 'DENTRA Admin',
                'password' => Hash::make('password'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Owner Role
        |--------------------------------------------------------------------------
        */

        $ownerRole = Role::where('slug', 'owner')->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Tenant Membership
        |--------------------------------------------------------------------------
        */

        $tenant->users()->syncWithoutDetaching([
            $user->id => [
                'role_id' => $ownerRole->id,
                'role' => 'owner',
                'is_active' => true,
            ],
        ]);
    }
}