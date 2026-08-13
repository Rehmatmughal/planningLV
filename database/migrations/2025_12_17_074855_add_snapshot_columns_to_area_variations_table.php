<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('area_variations', function (Blueprint $table) {

            // Snapshot at time of measurement
            $table->enum('road_status_at_time', ['complete','not_complete'])
                  ->nullable()
                  ->after('measured_area');

            $table->enum('sewer_status_at_time', ['constructed','not_constructed'])
                  ->nullable()
                  ->after('road_status_at_time');

            $table->enum('lop_status_at_time', ['lop','non_lop','mortgaged'])
                  ->nullable()
                  ->after('sewer_status_at_time');
        });
    }

    public function down()
    {
        Schema::table('area_variations', function (Blueprint $table) {
            $table->dropColumn([
                'road_status_at_time',
                'sewer_status_at_time',
                'lop_status_at_time'
            ]);
        });
    }
};
