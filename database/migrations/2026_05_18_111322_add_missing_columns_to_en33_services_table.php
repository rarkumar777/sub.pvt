<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToEn33ServicesTable extends Migration
{
    public function up()
    {
        Schema::table('en33_services', function (Blueprint $table) {
            $table->text('image')->nullable();
            $table->text('notes')->nullable();
            $table->string('acc_type')->nullable();
            $table->string('acc_category')->nullable();
            $table->string('website')->nullable();
            $table->string('arrival')->nullable();
            $table->string('transport_method')->nullable();
            $table->string('departure_location')->nullable();
            $table->string('arrival_destination')->nullable();
            $table->string('length_time')->nullable();
            $table->string('distance_km')->nullable();
        });
    }

    public function down()
    {
        Schema::table('en33_services', function (Blueprint $table) {
            $table->dropColumn([
                'image', 'notes', 'acc_type', 'acc_category', 'website', 
                'arrival', 'transport_method', 'departure_location', 
                'arrival_destination', 'length_time', 'distance_km'
            ]);
        });
    }
}
