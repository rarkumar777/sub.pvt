<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->decimal('agency_commission', 8, 2)->default(0)->after('nights_included');
            $table->string('commission_type')->default('percent')->after('agency_commission'); // percent or fixed
        });
    }
    public function down(): void {
        Schema::table('trip_itineraries', function (Blueprint $table) {
            $table->dropColumn(['agency_commission', 'commission_type']);
        });
    }
};
