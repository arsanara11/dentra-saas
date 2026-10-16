<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Link appointments to a doctor
|--------------------------------------------------------------------------
| Idempotent: safe to run again after a partial failure.
*/

return new class extends Migration
{
    private const INDEX = 'appointments_tenant_doctor_date_time_index';

    public function up(): void
    {
        if (! Schema::hasColumn('appointments', 'doctor_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('doctor_id')
                    ->nullable()
                    ->constrained('doctors')
                    ->nullOnDelete();
            });
        }

        if (! $this->indexExists(self::INDEX)) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->index(
                    ['tenant_id', 'doctor_id', 'appointment_date', 'start_time'],
                    self::INDEX
                );
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists(self::INDEX)) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropIndex(self::INDEX);
            });
        }

        if (Schema::hasColumn('appointments', 'doctor_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('doctor_id');
            });
        }
    }

    private function indexExists(string $name): bool
    {
        foreach (Schema::getIndexes('appointments') as $index) {
            if (strcasecmp($index['name'], $name) === 0) {
                return true;
            }
        }

        return false;
    }
};