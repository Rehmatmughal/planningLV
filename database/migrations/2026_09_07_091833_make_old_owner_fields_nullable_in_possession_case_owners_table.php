<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->change();
            $table->string('cnic')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('contact_no')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {
            $table->string('owner_name')->nullable(false)->change();
            $table->string('cnic')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('contact_no')->nullable(false)->change();
        });
    }
};
