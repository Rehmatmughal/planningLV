<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('possession_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plot_id');

            $table->enum('possession_status', [
                'possessionable',
                'non_lop_possessionable',
                'under_development_possessionable',
                'not_possessionable'
            ]);

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('plot_id')
                ->references('id')->on('plots')
                ->onDelete('cascade');

            $table->unique('plot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('possession_statuses');
    }
};
