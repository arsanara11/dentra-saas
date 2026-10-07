<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code');

            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();

            $table->string('postal_code')->nullable();

            $table->string('timezone')
                ->default('Asia/Jakarta');

            $table->boolean('is_main')
                ->default(false);

            $table->string('status')
                ->default('active');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};