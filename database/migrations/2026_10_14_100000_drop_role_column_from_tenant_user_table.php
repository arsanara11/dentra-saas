<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Remove the legacy tenant_user.role text column
|--------------------------------------------------------------------------
| Access control is driven by tenant_user.role_id (roles + permissions).
| The old text column was a second, unused source of truth.
|
| Before dropping it, any membership that still has a role name but no
| role_id gets its role_id filled in, so nobody loses access.
*/

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tenant_user', 'role')) {
            return;
        }

        $roleIds = DB::table('roles')->pluck('id', 'slug');

        foreach ($roleIds as $slug => $roleId) {
            DB::table('tenant_user')
                ->whereNull('role_id')
                ->where('role', $slug)
                ->update(['role_id' => $roleId]);
        }

        Schema::table('tenant_user', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('tenant_user', 'role')) {
            return;
        }

        Schema::table('tenant_user', function (Blueprint $table) {
            $table->string('role')->default('staff');
        });

        // Restore the role name from the role the user actually has.
        foreach (DB::table('roles')->pluck('slug', 'id') as $roleId => $slug) {
            DB::table('tenant_user')
                ->where('role_id', $roleId)
                ->update(['role' => $slug]);
        }
    }
};