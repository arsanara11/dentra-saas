
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();

            // Clinic and branch
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Patient and optional appointment
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->restrictOnDelete();

            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained('appointments')
                ->nullOnDelete();

            // Daily queue identification
            $table->date('queue_date');
            $table->unsignedInteger('queue_number');

            // waiting, called, serving, completed, cancelled
            $table->string('status', 30)->default('waiting');

            // Queue timestamps
            $table->timestamp('called_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // One queue number per clinic per day
            $table->unique(
                ['tenant_id', 'queue_date', 'queue_number'],
                'queues_tenant_date_number_unique'
            );

            $table->index(
                ['tenant_id', 'queue_date', 'status'],
                'queues_tenant_date_status_index'
            );

            $table->index(
                ['tenant_id', 'patient_id'],
                'queues_tenant_patient_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
