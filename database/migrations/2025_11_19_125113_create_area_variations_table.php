<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('area_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plot_id');

            // Actual measured area only
            // $table->decimal('measured_area', 10, 2)->default(0)->nullable();
            $table->decimal('measured_area', 10, 2)->nullable();

            // Optional notes
            $table->string('measured_by')->nullable();   // Surveyor name optional
            $table->date('measured_date')->nullable();   // Date of measurement
            $table->text('remarks')->nullable();
            // source indicates origin: 'survey' (normal), 'old_record' (imported historical), or any other string
            $table->string('source')->default('survey');

            $table->timestamps();

            $table->foreign('plot_id')
                ->references('id')->on('plots')
                ->onDelete('cascade');
            $table->index(['plot_id', 'measured_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_variations');
    }
};
