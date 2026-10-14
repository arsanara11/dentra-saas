<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropUnique(
                'queues_tenant_date_number_unique'
            );

            $table->unique(
                [
                    'tenant_id',
                    'branch_id',
                    'queue_date',
                    'queue_number',
                ],
                'queues_tenant_branch_date_number_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropUnique(
                'queues_tenant_branch_date_number_unique'
            );

            $table->unique(
                ['tenant_id', 'queue_date', 'queue_number'],
                'queues_tenant_date_number_unique'
            );
        });
    }
};