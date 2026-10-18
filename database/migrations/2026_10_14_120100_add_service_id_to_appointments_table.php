<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('appointments', 'service_id')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('appointments', 'service_id')) {
            return;
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};