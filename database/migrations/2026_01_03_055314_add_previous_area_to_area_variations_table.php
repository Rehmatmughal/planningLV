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
            $table->decimal('previous_area', 10, 2)
                ->after('plot_id')
                ->comment('Plot area before this variation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_variations', function (Blueprint $table) {
            $table->dropColumn([
                'previous_area'
                                
            ]);
        });
    }
};
