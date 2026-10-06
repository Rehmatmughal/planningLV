<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {

            $table->dropColumn([
                'owner_name',
                'cnic',
                'address',
                'contact_no',
            ]);

        });
    }

    public function down(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {

            $table->string('owner_name')->nullable();
            $table->string('cnic')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_no')->nullable();

            $table->index('cnic');

        });
    }
};