<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->text('payment_conditions')->nullable()->after('booking_conditions');
            $table->boolean('reduced_mobility')->default(false)->after('payment_conditions');
            $table->text('passports_visas')->nullable()->after('reduced_mobility');
            $table->text('travel_insurance')->nullable()->after('passports_visas');
        });
    }

    public function down(): void
    {
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->dropColumn(['payment_conditions', 'reduced_mobility', 'passports_visas', 'travel_insurance']);
        });
    }
};
