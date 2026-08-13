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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('block_name');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('remarks')->nullable();
            $table->timestamps();

            // $table->softDeletes(); // ✅ Soft delete column
            // Yeh line composite unique constraint lagayegi
            // $table->unique(['project_id', 'block_name']);
            $table->unique(['project_id', 'block_name'], 'unique_project_block');

        });

        // Schema::create('blocks', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
