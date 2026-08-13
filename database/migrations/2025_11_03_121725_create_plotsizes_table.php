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
        Schema::create('plotsizes', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g. 25x50, 30x60
            $table->decimal('size_area', 10, 2)->default(0); // numeric area (sqyd or sqft per your convention)
            $table->unsignedBigInteger('project_id');
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unique(['project_id', 'title'], 'unique_project_plotsize');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plotsizes');
    }
};
