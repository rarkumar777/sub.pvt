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
        Schema::table('trip_requests', function (Blueprint $table) {
            $table->json('travel_styles')->nullable()->after('accommodation_prefs');
            $table->string('departure_period')->nullable()->after('return_date');
            $table->string('approx_duration')->nullable()->after('departure_period');
            $table->boolean('is_honeymoon')->default(false)->after('participant_type');
            $table->string('group_type')->nullable()->after('is_honeymoon');
            $table->string('password')->nullable()->after('phone');
            $table->string('civility', 10)->nullable()->after('email');
            $table->string('dob', 20)->nullable()->after('phone');
            $table->string('country', 10)->nullable()->after('phone');
            $table->boolean('marketing_consent')->default(false)->after('notes');
            $table->boolean('terms_consent')->default(false)->after('marketing_consent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_requests', function (Blueprint $table) {
            $table->dropColumn([
                'travel_styles', 'departure_period', 'approx_duration',
                'is_honeymoon', 'group_type', 'password', 'civility', 'dob', 'country',
                'marketing_consent', 'terms_consent',
            ]);
        });
    }
};
