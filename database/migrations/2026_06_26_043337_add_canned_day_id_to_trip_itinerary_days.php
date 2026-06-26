<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_itinerary_days', function (Blueprint $table) {
            $table->unsignedBigInteger('canned_day_id')->nullable()->after('trip_itinerary_id');
        });
    }

    public function down(): void
    {
        Schema::table('trip_itinerary_days', function (Blueprint $table) {
            $table->dropColumn('canned_day_id');
        });
    }
};
