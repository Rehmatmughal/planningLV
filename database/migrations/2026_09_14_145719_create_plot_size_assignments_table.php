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
        Schema::create('plot_size_assignments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('property_type_id');
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('plotsize_id');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('property_type_id')
                ->references('id')
                ->on('property_types')
                ->onDelete('cascade');

            $table->foreign('block_id')
                ->references('id')
                ->on('blocks')
                ->onDelete('cascade');

            $table->foreign('plotsize_id')
                ->references('id')
                ->on('plotsizes')
                ->onDelete('cascade');

            // Prevent duplicate assignments
            $table->unique(
                [
                    'project_id',
                    'property_type_id',
                    'block_id',
                    'plotsize_id'
                ],
                'unique_plot_size_assignment'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_size_assignments');
    }
};