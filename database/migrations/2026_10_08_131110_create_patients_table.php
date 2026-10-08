<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Tenant
            |--------------------------------------------------------------------------
            */

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Patient Identity
            |--------------------------------------------------------------------------
            */

            $table->string('patient_number', 30);

            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('gender', 20)->nullable();

            $table->date('date_of_birth')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Emergency Contact
            |--------------------------------------------------------------------------
            */

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('emergency_contact_relation')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Medical Information
            |--------------------------------------------------------------------------
            */

            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Clinic Information
            |--------------------------------------------------------------------------
            */

            $table->string('status', 20)->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'tenant_id',
                'patient_number',
            ]);

            $table->index([
                'tenant_id',
                'status',
            ]);

            $table->index([
                'tenant_id',
                'phone',
            ]);

            $table->index([
                'tenant_id',
                'email',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};