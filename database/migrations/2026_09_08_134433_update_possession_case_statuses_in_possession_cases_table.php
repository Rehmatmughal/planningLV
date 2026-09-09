<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE possession_cases
            MODIFY current_status ENUM(
                'received',
                'prepared',
                'surveyor_signed',
                'approval',
                'town_planner_signed',
                'completed'
            ) DEFAULT 'received'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE possession_cases
            MODIFY current_status ENUM(
                'received',
                'prepared',
                'signed',
                'approval',
                'receive_back',
                'handed_over',
                'completed'
            ) DEFAULT 'received'
        ");
    }
};
