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
        Schema::table('area_variations', function (Blueprint $table) {
            $table->enum('overall_status_at_time', [
                'developed',
                'under_development',
                'not_developed'
            ])->nullable()->after('lop_status_at_time');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::table('area_variations', function (Blueprint $table) {
            Schema::table('area_variations', function (Blueprint $table) {
                $table->dropColumn('overallstatus_at_time');
            });
        });
    }
};
