<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Tenant and clinic branch
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Patient information
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->restrictOnDelete();

            // Appointment identification
            $table->string('appointment_number', 50);
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();

            // Appointment status
            $table->string('status', 30)->default('scheduled');

            // Visit details
            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();

            // User who created the appointment
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate appointment numbers within a clinic
            $table->unique(
                ['tenant_id', 'appointment_number'],
                'appointments_tenant_number_unique'
            );

            // Improve filtering by date and status
            $table->index(
                ['tenant_id', 'appointment_date', 'status'],
                'appointments_tenant_date_status_index'
            );

            $table->index(
                ['tenant_id', 'patient_id'],
                'appointments_tenant_patient_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

