<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DentraRbacSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Dashboard
            [
                'name' => 'View Dashboard',
                'slug' => 'dashboard.view',
                'group' => 'dashboard',
                'description' => 'View clinic dashboard.',
            ],

            // Patients
            [
                'name' => 'View Patients',
                'slug' => 'patients.view',
                'group' => 'patients',
                'description' => 'View patient records.',
            ],
            [
                'name' => 'Create Patients',
                'slug' => 'patients.create',
                'group' => 'patients',
                'description' => 'Create new patients.',
            ],
            [
                'name' => 'Update Patients',
                'slug' => 'patients.update',
                'group' => 'patients',
                'description' => 'Update patient information.',
            ],
            [
                'name' => 'Delete Patients',
                'slug' => 'patients.delete',
                'group' => 'patients',
                'description' => 'Delete patient records.',
            ],

            // Appointments
            [
                'name' => 'View Appointments',
                'slug' => 'appointments.view',
                'group' => 'appointments',
                'description' => 'View appointments.',
            ],
            [
                'name' => 'Create Appointments',
                'slug' => 'appointments.create',
                'group' => 'appointments',
                'description' => 'Create appointments.',
            ],
            [
                'name' => 'Update Appointments',
                'slug' => 'appointments.update',
                'group' => 'appointments',
                'description' => 'Update appointments.',
            ],
            [
                'name' => 'Cancel Appointments',
                'slug' => 'appointments.cancel',
                'group' => 'appointments',
                'description' => 'Cancel appointments.',
            ],

            // Queue
            [
                'name' => 'View Queue',
                'slug' => 'queue.view',
                'group' => 'queue',
                'description' => 'View clinic queue.',
            ],
            [
                'name' => 'Manage Queue',
                'slug' => 'queue.manage',
                'group' => 'queue',
                'description' => 'Call, skip, recall, and manage queue tickets.',
            ],

            // Doctors
            [
                'name' => 'View Doctors',
                'slug' => 'doctors.view',
                'group' => 'doctors',
                'description' => 'View doctors.',
            ],
            [
                'name' => 'Manage Doctors',
                'slug' => 'doctors.manage',
                'group' => 'doctors',
                'description' => 'Create and manage doctors.',
            ],

            // Dental Records
            [
                'name' => 'View Dental Records',
                'slug' => 'dental_records.view',
                'group' => 'clinical',
                'description' => 'View dental records.',
            ],
            [
                'name' => 'Create Dental Records',
                'slug' => 'dental_records.create',
                'group' => 'clinical',
                'description' => 'Create dental records.',
            ],
            [
                'name' => 'Update Dental Records',
                'slug' => 'dental_records.update',
                'group' => 'clinical',
                'description' => 'Update dental records.',
            ],

            // Treatments
            [
                'name' => 'View Treatments',
                'slug' => 'treatments.view',
                'group' => 'clinical',
                'description' => 'View treatments.',
            ],
            [
                'name' => 'Manage Treatments',
                'slug' => 'treatments.manage',
                'group' => 'clinical',
                'description' => 'Manage treatment plans and treatments.',
            ],

            // Prescriptions
            [
                'name' => 'View Prescriptions',
                'slug' => 'prescriptions.view',
                'group' => 'clinical',
                'description' => 'View prescriptions.',
            ],
            [
                'name' => 'Manage Prescriptions',
                'slug' => 'prescriptions.manage',
                'group' => 'clinical',
                'description' => 'Create and manage prescriptions.',
            ],

            // Billing
            [
                'name' => 'View Billing',
                'slug' => 'billing.view',
                'group' => 'billing',
                'description' => 'View invoices and billing.',
            ],
            [
                'name' => 'Manage Billing',
                'slug' => 'billing.manage',
                'group' => 'billing',
                'description' => 'Create invoices and manage billing.',
            ],
            [
                'name' => 'Process Payments',
                'slug' => 'payments.process',
                'group' => 'billing',
                'description' => 'Process patient payments.',
            ],
            [
                'name' => 'Manage Refunds',
                'slug' => 'refunds.manage',
                'group' => 'billing',
                'description' => 'Manage payment refunds.',
            ],

            // Inventory
            [
                'name' => 'View Inventory',
                'slug' => 'inventory.view',
                'group' => 'inventory',
                'description' => 'View inventory.',
            ],
            [
                'name' => 'Manage Inventory',
                'slug' => 'inventory.manage',
                'group' => 'inventory',
                'description' => 'Manage inventory items and stock.',
            ],
            [
                'name' => 'Manage Suppliers',
                'slug' => 'suppliers.manage',
                'group' => 'inventory',
                'description' => 'Manage suppliers.',
            ],
            [
                'name' => 'Manage Purchase Orders',
                'slug' => 'purchase_orders.manage',
                'group' => 'inventory',
                'description' => 'Manage purchase orders.',
            ],

            // Reports
            [
                'name' => 'View Reports',
                'slug' => 'reports.view',
                'group' => 'reports',
                'description' => 'View clinic reports.',
            ],

            // Staff
            [
                'name' => 'View Staff',
                'slug' => 'staff.view',
                'group' => 'staff',
                'description' => 'View clinic staff.',
            ],
            [
                'name' => 'Manage Staff',
                'slug' => 'staff.manage',
                'group' => 'staff',
                'description' => 'Manage clinic staff and access.',
            ],

            // Branches
            [
                'name' => 'View Branches',
                'slug' => 'branches.view',
                'group' => 'branches',
                'description' => 'View clinic branches.',
            ],
            [
                'name' => 'Manage Branches',
                'slug' => 'branches.manage',
                'group' => 'branches',
                'description' => 'Manage clinic branches.',
            ],

            // Settings
            [
                'name' => 'View Settings',
                'slug' => 'settings.view',
                'group' => 'settings',
                'description' => 'View clinic settings.',
            ],
            [
                'name' => 'Manage Settings',
                'slug' => 'settings.manage',
                'group' => 'settings',
                'description' => 'Manage clinic settings.',
            ],

            // Audit
            [
                'name' => 'View Audit Logs',
                'slug' => 'audit_logs.view',
                'group' => 'security',
                'description' => 'View audit logs.',
            ],

            // Platform
            [
                'name' => 'Manage Clinics',
                'slug' => 'platform.clinics.manage',
                'group' => 'platform',
                'description' => 'Manage DENTRA clinics.',
            ],
            [
                'name' => 'Manage Subscriptions',
                'slug' => 'platform.subscriptions.manage',
                'group' => 'platform',
                'description' => 'Manage SaaS subscriptions.',
            ],
            [
                'name' => 'View Platform Analytics',
                'slug' => 'platform.analytics.view',
                'group' => 'platform',
                'description' => 'View platform-wide analytics.',
            ],
            [
                'name' => 'View System Health',
                'slug' => 'platform.system_health.view',
                'group' => 'platform',
                'description' => 'View platform system health.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Full access to the DENTRA platform.',
                'is_system' => true,
            ],
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'Clinic owner with full clinic-level access.',
                'is_system' => true,
            ],
            [
                'name' => 'Clinic Admin',
                'slug' => 'clinic_admin',
                'description' => 'Administrative manager of a dental clinic.',
                'is_system' => true,
            ],
            [
                'name' => 'Receptionist',
                'slug' => 'receptionist',
                'description' => 'Handles patients, appointments, and queue operations.',
                'is_system' => true,
            ],
            [
                'name' => 'Dentist',
                'slug' => 'dentist',
                'description' => 'Handles clinical and dental treatment workflows.',
                'is_system' => true,
            ],
            [
                'name' => 'Assistant',
                'slug' => 'assistant',
                'description' => 'Assists dentists with clinical operations.',
                'is_system' => true,
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Handles billing, payments, and financial reports.',
                'is_system' => true,
            ],
            [
                'name' => 'Patient',
                'slug' => 'patient',
                'description' => 'Patient portal access.',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Role → Permission Mapping
        |--------------------------------------------------------------------------
        */

        $allPermissions = Permission::pluck('id', 'slug');

        $rolePermissions = [
            'super_admin' => [
                '*',
            ],

            'owner' => [
                'dashboard.view',

                'patients.view',
                'patients.create',
                'patients.update',
                'patients.delete',

                'appointments.view',
                'appointments.create',
                'appointments.update',
                'appointments.cancel',

                'queue.view',
                'queue.manage',

                'doctors.view',
                'doctors.manage',

                'dental_records.view',
                'dental_records.create',
                'dental_records.update',

                'treatments.view',
                'treatments.manage',

                'prescriptions.view',
                'prescriptions.manage',

                'billing.view',
                'billing.manage',
                'payments.process',
                'refunds.manage',

                'inventory.view',
                'inventory.manage',
                'suppliers.manage',
                'purchase_orders.manage',

                'reports.view',

                'staff.view',
                'staff.manage',

                'branches.view',
                'branches.manage',

                'settings.view',
                'settings.manage',

                'audit_logs.view',
            ],

            'clinic_admin' => [
                'dashboard.view',

                'patients.view',
                'patients.create',
                'patients.update',

                'appointments.view',
                'appointments.create',
                'appointments.update',
                'appointments.cancel',

                'queue.view',
                'queue.manage',

                'doctors.view',

                'dental_records.view',

                'treatments.view',

                'prescriptions.view',

                'billing.view',
                'billing.manage',
                'payments.process',

                'inventory.view',
                'inventory.manage',
                'suppliers.manage',
                'purchase_orders.manage',

                'reports.view',

                'staff.view',
                'staff.manage',

                'branches.view',

                'settings.view',
                'settings.manage',

                'audit_logs.view',
            ],

            'receptionist' => [
                'dashboard.view',

                'patients.view',
                'patients.create',
                'patients.update',

                'appointments.view',
                'appointments.create',
                'appointments.update',
                'appointments.cancel',

                'queue.view',
                'queue.manage',

                'doctors.view',

                'billing.view',
                'payments.process',
            ],

            'dentist' => [
                'dashboard.view',

                'patients.view',

                'appointments.view',
                'appointments.update',

                'queue.view',

                'doctors.view',

                'dental_records.view',
                'dental_records.create',
                'dental_records.update',

                'treatments.view',
                'treatments.manage',

                'prescriptions.view',
                'prescriptions.manage',
            ],

            'assistant' => [
                'dashboard.view',

                'patients.view',

                'appointments.view',

                'queue.view',

                'doctors.view',

                'dental_records.view',

                'treatments.view',

                'prescriptions.view',
            ],

            'finance' => [
                'dashboard.view',

                'patients.view',

                'appointments.view',

                'billing.view',
                'billing.manage',
                'payments.process',
                'refunds.manage',

                'inventory.view',

                'reports.view',
            ],

            'patient' => [
                'appointments.view',
                'appointments.create',

                'dental_records.view',

                'prescriptions.view',

                'billing.view',
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $role = Role::where('slug', $roleSlug)->firstOrFail();

            if (in_array('*', $permissionSlugs, true)) {
                $role->permissions()->sync(
                    $allPermissions->values()->all()
                );

                continue;
            }

            $permissionIds = collect($permissionSlugs)
                ->map(fn (string $slug) => $allPermissions->get($slug))
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}