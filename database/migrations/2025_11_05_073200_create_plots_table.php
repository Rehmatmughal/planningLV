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
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->string('pid_lv')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('street_id');
            $table->string('plot_number');
            $table->unsignedBigInteger('size_id');

            // $table->decimal('measured_plotarea', 10, 2)->default(0);
            // $table->string('measuredby_plotarea')->nullable();
            // $table->date('measured_date')->nullable();
            $table->unsignedBigInteger('category_id');
            // $table->string('category')->nullable();
            $table->enum('numbering_type', ['blockwise', 'streetwise']);
            $table->text('remarks')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');
            $table->foreign('block_id')
                ->references('id')->on('blocks')
                ->onDelete('cascade');
            $table->foreign('street_id')
                ->references('id')->on('streets')
                ->onDelete('cascade');
             $table->foreign('category_id')
                ->references('id')->on('plot_category_types')
                ->onDelete('cascade');    
            // $table->foreign('size_id')
            //     ->references('id')->on('plotsizes')
            //     ->onDelete('cascade');

            // indexes for performance
            $table->index(['project_id', 'block_id', 'plot_number']);
            $table->index(['project_id', 'block_id', 'street_id', 'plot_number']);

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
