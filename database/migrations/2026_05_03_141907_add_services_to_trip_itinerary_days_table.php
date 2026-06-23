<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_itinerary_days', function (Blueprint $table) {
            if (!Schema::hasColumn('trip_itinerary_days', 'services')) {
                $table->json('services')->nullable()->after('photos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trip_itinerary_days', function (Blueprint $table) {
            $table->dropColumn('services');
        });
    }
};
