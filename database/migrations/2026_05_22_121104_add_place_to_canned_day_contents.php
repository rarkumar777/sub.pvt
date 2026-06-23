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
        Schema::table('en33_tours_canned_days_contents', function (Blueprint $table) {
            $table->string('place')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('en33_tours_canned_days_contents', function (Blueprint $table) {
            $table->dropColumn('place');
        });
    }
};
