<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('services')) {
            return;
        }

        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('code', 30);
            $table->string('name', 150);
            $table->string('category', 50)->nullable();
            $table->text('description')->nullable();

            // Default length of the treatment, used to fill the
            // appointment end time.
            $table->unsignedSmallInteger('duration_minutes')->default(30);

            // Base price in the clinic's currency.
            $table->decimal('price', 12, 2)->default(0);

            // active / inactive. A service that has been used is
            // deactivated instead of deleted, so history is kept.
            $table->string('status', 20)->default('active');

            $table->timestamps();

            $table->unique(['tenant_id', 'code'], 'services_tenant_code_unique');
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};