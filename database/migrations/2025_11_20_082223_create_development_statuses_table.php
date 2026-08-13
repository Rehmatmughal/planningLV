<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('development_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plot_id');


            // Development fields
            // $table->boolean('is_in_possession')->default(false);
            // $table->enum('possession_status', ['in_possession', 'not_in_possession']);
            $table->enum('sewer_manholes', ['constructed', 'not_constructed']);
            $table->enum('asphalt_tst', ['yes', 'no']);
            $table->enum('overall_status', ['developed', 'under_development', 'not_developed']);

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('plot_id')
                ->references('id')->on('plots')
                ->onDelete('cascade');

            // one status per plot
            $table->unique('plot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_statuses');
    }
};

