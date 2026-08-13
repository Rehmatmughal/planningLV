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
        Schema::create('streets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('block_id');
            $table->string('street_name');
            // $table->enum('numbering_type', ['blockwise', 'streetwise']);
            $table->text('remarks')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');
            $table->foreign('block_id')
                ->references('id')->on('blocks')
                ->onDelete('cascade');

            // unique street per block in a project
            $table->unique(['project_id', 'block_id', 'street_name',], 'unique_project_block_street');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streets');
    }
};
