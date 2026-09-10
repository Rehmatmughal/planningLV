<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {
            $table->dropColumn('ownership_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('possession_case_owners', function (Blueprint $table) {
            $table->decimal('ownership_percentage', 5, 2)->nullable();
        });
    }
};