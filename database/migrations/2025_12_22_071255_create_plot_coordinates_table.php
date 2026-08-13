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
        Schema::create('plot_coordinates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plot_id');
            $table->decimal('Easting', 10, 2)->default(0); // e.g. Easting 
            $table->decimal('Northing', 10, 2)->default(0); // e.g. Northing
            $table->decimal('Rotation', 10, 2)->default(0); // e.g. Rotation angle 
            $table->decimal('UTM_E', 10, 2)->default(327464.00); // e.g. UTM Easting 
            $table->decimal('UTM_N', 10, 2)->default(3728900.00); // e.g. UTM Northing
            $table->decimal('latitude', 10, 8)->default(33.595517); // e.g. latitude 
            $table->decimal('longitude', 11, 8)->default(73.138750); // e.g. Long

            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('plot_id')->references('id')->on('plots')->onDelete('cascade');
            $table->unique('plot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_coordinates');
    }
};
