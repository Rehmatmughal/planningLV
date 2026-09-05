<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {

            $table->foreignId('owner_id')
                ->nullable()
                ->after('possession_case_id')
                ->constrained('owners')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {

            $table->dropForeign(['owner_id']);

            $table->dropColumn('owner_id');

        });
    }
    
};
