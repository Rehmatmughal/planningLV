<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('possession_cases', function (Blueprint $table) {
            $table->date('town_planner_signed_at')
                ->nullable()
                ->after('signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('possession_cases', function (Blueprint $table) {
            $table->dropColumn('town_planner_signed_at');
        });
    }
};
