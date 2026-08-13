<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mortgage_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plot_id');

            $table->enum('is_mortgaged', ['yes', 'no']);

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
        Schema::dropIfExists('mortgage_statuses');
    }
};

