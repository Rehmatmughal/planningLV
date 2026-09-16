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
        Schema::create('owners', function (Blueprint $table) {

            $table->id();

            $table->string('owner_name');

            // CNIC unique hoga - same CNIC dobara owner ke taur par create nahi ho sakega
            $table->string('cnic')->unique();

            $table->text('address')->nullable();

            $table->string('contact_no')->nullable();

            $table->timestamps();

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }

};
