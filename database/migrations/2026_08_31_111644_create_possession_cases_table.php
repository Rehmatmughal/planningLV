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

            // Case number for this plot
            $table->unsignedInteger('case_no');

            // Approval
            $table->boolean('need_approval')->default(false);

            // Current case status
            $table->enum('current_status', [
                'received',
                'prepared',
                'signed',
                'approval',
                'receive_back',
                'handed_over',
                'completed',
            ])->default('received');

            // Current holder
            $table->string('current_holder_type')->nullable();
            $table->unsignedBigInteger('current_holder_id')->nullable();
            $table->string('current_holder_name')->nullable();

            // Important dates
            $table->date('received_at')->nullable();
            $table->date('prepared_at')->nullable();
            $table->date('signed_at')->nullable();
            $table->date('approval_sent_at')->nullable();
            $table->date('received_back_at')->nullable();
            $table->date('handed_over_at')->nullable();
            $table->date('completed_at')->nullable();

            // Handover
            $table->string('handed_over_to')->nullable();

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

            // One case number can occur only once for a plot
            $table->unique(['plot_id', 'case_no']);

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