<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('possession_case_owners', function (Blueprint $table) {

            $table->id();

            // Possession Case
            $table->foreignId('possession_case_id')
                ->constrained('possession_cases')
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('owners')
                ->nullOnDelete();

            // Owner Information
            $table->string('owner_name')
                ->nullable();

            $table->string('cnic')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('contact_no')->nullable()
                ->nullable();

            $table->text('address_snapshot')
                ->nullable();

            $table->timestamps();

            // Useful index
            $table->index('cnic');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('possession_case_owners');
    }
};