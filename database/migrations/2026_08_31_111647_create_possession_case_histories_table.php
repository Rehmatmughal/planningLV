<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('possession_case_histories', function (Blueprint $table) {

            $table->id();

            // Possession Case
            $table->foreignId('possession_case_id')
                ->constrained('possession_cases')
                ->cascadeOnDelete();

            // Plot reference for easier reporting/filtering
            $table->foreignId('plot_id')
                ->constrained('plots')
                ->cascadeOnDelete();

            // Action performed
            $table->string('action');

            // Status before action
            $table->string('old_status')->nullable();

            // Status after action
            $table->string('new_status')->nullable();

            // Holder before action
            $table->string('old_holder')->nullable();

            // Holder after action
            $table->string('new_holder')->nullable();

            // Person/department to whom case was handed over
            $table->string('handed_over_to')->nullable();

            // Remarks for this particular action
            $table->text('remarks')->nullable();

            // User who performed this action
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Useful indexes
            $table->index('plot_id');
            $table->index('new_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('possession_case_histories');
    }
};