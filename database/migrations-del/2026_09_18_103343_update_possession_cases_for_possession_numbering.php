<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('possession_cases', function (Blueprint $table) {

            // Full possession number
            $table->string('possession_no')
                ->nullable()
                ->after('plot_id');

            // Optional reference number
            $table->string('reference_no')
                ->nullable()
                ->after('possession_no');

            // Numeric sequence used for automatic numbering
            $table->unsignedInteger('possession_sequence')
                ->default(0)
                ->after('reference_no');

            // Re-possession revision
            // 0 = first possession
            // 1 = T1
            // 2 = T2
            // 3 = T3
            $table->unsignedInteger('revision_no')
                ->default(0)
                ->after('possession_sequence');

            // Cancellation information
            $table->date('cancelled_at')
                ->nullable()
                ->after('completed_at');

            $table->foreignId('cancelled_by')
                ->nullable()
                ->after('cancelled_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('cancellation_reason')
                ->nullable()
                ->after('cancelled_by');
            
        });
    }

    public function down(): void
    {
        Schema::table('possession_cases', function (Blueprint $table) {

            $table->dropForeign(['cancelled_by']);

            $table->dropColumn([
                'possession_no',
                'reference_no',
                'possession_sequence',
                'revision_no',
                'cancelled_at',
                'cancelled_by',
                'cancellation_reason',
            ]);
        });
    }
};