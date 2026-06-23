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
        Schema::table('trip_itinerary_days', function (Blueprint $table) {
            $table->string('accommodation_stars')->nullable()->after('accommodation_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_itinerary_days', function (Blueprint $table) {
            $table->dropColumn('accommodation_stars');
        });
    }
};
