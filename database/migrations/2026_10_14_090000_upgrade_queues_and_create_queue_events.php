<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Upgrade queues + create queue_events
|--------------------------------------------------------------------------
| Idempotent: every step checks the current database state first, so the
| migration can be re-run safely after a partial failure (MySQL DDL is
| not transactional).
*/

return new class extends Migration
{
    private const NEW_UNIQUE = 'queues_branch_date_prefix_number_unique';
    private const DOCTOR_INDEX = 'queues_tenant_doctor_date_status_index';

    /** Columns of the legacy unique index we are replacing. */
    private const LEGACY_UNIQUE_COLUMNS = [
        'tenant_id',
        'queue_date',
        'queue_number',
    ];

    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Upgrade queues table (add missing columns only)
        |--------------------------------------------------------------------------
        */

        $needsPrefix = ! Schema::hasColumn('queues', 'prefix');
        $needsDoctor = ! Schema::hasColumn('queues', 'doctor_id');
        $needsPriority = ! Schema::hasColumn('queues', 'priority');
        $needsSkippedAt = ! Schema::hasColumn('queues', 'skipped_at');
        $needsUpdatedBy = ! Schema::hasColumn('queues', 'updated_by');

        Schema::table('queues', function (Blueprint $table) use (
            $needsPrefix,
            $needsDoctor,
            $needsPriority,
            $needsSkippedAt,
            $needsUpdatedBy
        ) {
            if ($needsPrefix) {
                // Ticket prefix, e.g. A-001, B-001
                $table->string('prefix', 5)->default('A');
            }

            if ($needsDoctor) {
                $table->foreignId('doctor_id')
                    ->nullable()
                    ->constrained('doctors')
                    ->nullOnDelete();
            }

            if ($needsPriority) {
                // normal, priority, emergency
                $table->string('priority', 20)->default('normal');
            }

            if ($needsSkippedAt) {
                $table->timestamp('skipped_at')->nullable();
            }

            if ($needsUpdatedBy) {
                $table->foreignId('updated_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Backfill branch_id
        |--------------------------------------------------------------------------
        | NULL values never collide in a unique index, so a NULL branch_id
        | would make the new unique index useless.
        */

        $tenantIds = DB::table('queues')
            ->whereNull('branch_id')
            ->distinct()
            ->pluck('tenant_id');

        foreach ($tenantIds as $tenantId) {
            $branchId = DB::table('branches')
                ->where('tenant_id', $tenantId)
                ->whereNull('deleted_at')
                ->orderByDesc('is_main')
                ->orderBy('id')
                ->value('id');

            if ($branchId) {
                DB::table('queues')
                    ->where('tenant_id', $tenantId)
                    ->whereNull('branch_id')
                    ->update(['branch_id' => $branchId]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Add new indexes (if missing)
        |--------------------------------------------------------------------------
        | Created before the legacy unique is dropped so MySQL always has
        | an index starting with tenant_id for the foreign key.
        */

        if (! $this->indexExists(self::NEW_UNIQUE)) {
            Schema::table('queues', function (Blueprint $table) {
                $table->unique(
                    ['tenant_id', 'branch_id', 'queue_date', 'prefix', 'queue_number'],
                    self::NEW_UNIQUE
                );
            });
        }

        if (! $this->indexExists(self::DOCTOR_INDEX)) {
            Schema::table('queues', function (Blueprint $table) {
                $table->index(
                    ['tenant_id', 'doctor_id', 'queue_date', 'status'],
                    self::DOCTOR_INDEX
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Drop legacy unique index (found by columns, not by name)
        |--------------------------------------------------------------------------
        */

        foreach ($this->legacyUniqueIndexNames() as $indexName) {
            Schema::table('queues', function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Queue events (audit trail, append-only)
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasTable('queue_events')) {
            Schema::create('queue_events', function (Blueprint $table) {
                $table->id();

                $table->foreignId('tenant_id')
                    ->constrained('tenants')
                    ->cascadeOnDelete();

                $table->foreignId('queue_id')
                    ->constrained('queues')
                    ->cascadeOnDelete();

                // created, called, recalled, serving, completed,
                // skipped, no_show, cancelled
                $table->string('event_type', 30);

                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30)->nullable();

                $table->foreignId('actor_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->json('metadata')->nullable();

                $table->timestamp('created_at')->useCurrent();

                $table->index(
                    ['tenant_id', 'queue_id', 'created_at'],
                    'queue_events_tenant_queue_created_index'
                );

                $table->index(
                    ['tenant_id', 'created_at'],
                    'queue_events_tenant_created_index'
                );
            });

            // Backfill only on first creation so events are never duplicated.
            DB::table('queues')
                ->orderBy('id')
                ->chunkById(500, function ($queues) {
                    $rows = [];

                    foreach ($queues as $queue) {
                        $rows[] = [
                            'tenant_id' => $queue->tenant_id,
                            'queue_id' => $queue->id,
                            'event_type' => 'created',
                            'from_status' => null,
                            'to_status' => 'waiting',
                            'actor_id' => $queue->created_by,
                            'metadata' => json_encode([
                                'backfilled' => true,
                                'current_status' => $queue->status,
                            ]),
                            'created_at' => $queue->created_at,
                        ];
                    }

                    if ($rows) {
                        DB::table('queue_events')->insert($rows);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_events');

        // Restore the legacy unique index (may fail if multiple branches
        // already share the same number on the same day).
        if (empty($this->legacyUniqueIndexNames())) {
            Schema::table('queues', function (Blueprint $table) {
                $table->unique(
                    self::LEGACY_UNIQUE_COLUMNS,
                    'queues_tenant_date_number_unique'
                );
            });
        }

        if ($this->indexExists(self::NEW_UNIQUE)) {
            Schema::table('queues', function (Blueprint $table) {
                $table->dropUnique(self::NEW_UNIQUE);
            });
        }

        if ($this->indexExists(self::DOCTOR_INDEX)) {
            Schema::table('queues', function (Blueprint $table) {
                $table->dropIndex(self::DOCTOR_INDEX);
            });
        }

        foreach (['doctor_id', 'updated_by'] as $column) {
            if (Schema::hasColumn('queues', $column)) {
                Schema::table('queues', function (Blueprint $table) use ($column) {
                    $table->dropConstrainedForeignId($column);
                });
            }
        }

        foreach (['prefix', 'priority', 'skipped_at'] as $column) {
            if (Schema::hasColumn('queues', $column)) {
                Schema::table('queues', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function indexExists(string $name): bool
    {
        foreach (Schema::getIndexes('queues') as $index) {
            if (strcasecmp($index['name'], $name) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Names of unique indexes whose columns are exactly
     * tenant_id + queue_date + queue_number (any name, any order).
     *
     * @return list<string>
     */
    private function legacyUniqueIndexNames(): array
    {
        $target = self::LEGACY_UNIQUE_COLUMNS;
        sort($target);

        $names = [];

        foreach (Schema::getIndexes('queues') as $index) {
            if (! ($index['unique'] ?? false) || ($index['primary'] ?? false)) {
                continue;
            }

            if (strcasecmp($index['name'], self::NEW_UNIQUE) === 0) {
                continue;
            }

            $columns = $index['columns'];
            sort($columns);

            if ($columns === $target) {
                $names[] = $index['name'];
            }
        }

        return $names;
    }
};