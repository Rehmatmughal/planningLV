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
            $table->enum('mortgage_status_at_time', ['yes', 'no'])
                ->nullable()
                ->after('overall_status_at_time');
            $table->unsignedTinyInteger('workflow_status')
                ->default(1)
                ->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('area_variations', function (Blueprint $table) {
            $table->dropColumn([
                'mortgage_status_at_time',
                'workflow_status',
                ]);
        });
    }
    
};
