<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('possession_cases', function (Blueprint $table) {

            $table->id();

            // Plot
            $table->foreignId('plot_id')
                ->constrained('plots')
                ->cascadeOnDelete();

            // Possession numbering
            $table->string('possession_no')->nullable();
            $table->string('reference_no')->nullable();

            $table->unsignedInteger('possession_sequence');
            $table->unsignedInteger('revision_no')->default(0);

            // Approval
            $table->boolean('need_approval')->default(false);

            // Current case status
            $table->enum('current_status', [
                'received',
                'prepared',
                'surveyor_signed',
                'approval',
                'town_planner_signed',
                'completed',
                // 'received',
                // 'prepared',
                // 'signed',
                // 'approval',
                // 'receive_back',
                // 'handed_over',
                // 'completed',
                'cancelled',
            ])->default('received');

            // Current holder
            $table->string('current_holder_type')->nullable();
            $table->unsignedBigInteger('current_holder_id')->nullable();
            $table->string('current_holder_name')->nullable();

            // Important dates
            $table->date('received_at')->nullable();
            $table->date('prepared_at')->nullable();
            $table->date('surveyor_signed_at')->nullable();
            $table->date('signed_at')->nullable();
            $table->date('town_planner_signed_at')->nullable();
            $table->date('approval_sent_at')->nullable();
            $table->date('received_back_at')->nullable();
            $table->date('handed_over_at')->nullable();
            $table->date('completed_at')->nullable();

            // Handover
            $table->string('handed_over_to')->nullable();

            // Cancellation
            $table->date('cancelled_at')->nullable();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('cancellation_reason')->nullable();

            // Remarks
            $table->text('remarks')->nullable();

            // Active case
            $table->boolean('is_active')->default(true);

            // Users
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Soft delete
            $table->softDeletes();

            

            // Useful indexes
            $table->index(['plot_id', 'is_active']);
            $table->index('current_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('possession_cases');
    }
};