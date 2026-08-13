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
        Schema::create('plot_category_types', function (Blueprint $table) {
            $table->id();
            $table->string('category_title')->default('General'); // e.g. Main road, Parkface, Corner
            // $table->unsignedBigInteger('project_id');
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unique('category_title');

            // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            // $table->unique(['project_id', 'category_title'], 'unique_project_plotsize');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_category_types');
    }
    
};
