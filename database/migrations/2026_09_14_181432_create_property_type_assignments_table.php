<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_type_assignments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('block_id');
            $table->unsignedBigInteger('property_type_id');

            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('block_id')
                ->references('id')
                ->on('blocks')
                ->onDelete('cascade');

            $table->foreign('property_type_id')
                ->references('id')
                ->on('property_types')
                ->onDelete('cascade');

            $table->unique(
                [
                    'project_id',
                    'block_id',
                    'property_type_id',
                ],
                'unique_property_type_assignment'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_type_assignments');
    }
};
