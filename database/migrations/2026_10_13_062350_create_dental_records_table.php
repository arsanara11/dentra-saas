<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dental_records', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('patient_id')
                ->constrained('patients')
                ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                ->constrained('doctors')
                ->restrictOnDelete();

            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained('appointments')
                ->nullOnDelete();

            $table->dateTime('examined_at');

            $table->text('chief_complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('prescription')->nullable();
            $table->text('clinical_notes')->nullable();

            $table->date('next_visit_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['tenant_id', 'patient_id']);
            $table->index(['tenant_id', 'doctor_id']);
            $table->index(['tenant_id', 'examined_at']);
        });
    }

    public function down(): void
    {
        Schema::table('dental_records', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'patient_id']);
            $table->dropIndex(['tenant_id', 'doctor_id']);
            $table->dropIndex(['tenant_id', 'examined_at']);

            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('appointment_id');
            $table->dropConstrainedForeignId('doctor_id');
            $table->dropConstrainedForeignId('patient_id');
            $table->dropConstrainedForeignId('tenant_id');

            $table->dropColumn([
                'examined_at',
                'chief_complaint',
                'diagnosis',
                'treatment',
                'prescription',
                'clinical_notes',
                'next_visit_at',
            ]);
        });
    }
};