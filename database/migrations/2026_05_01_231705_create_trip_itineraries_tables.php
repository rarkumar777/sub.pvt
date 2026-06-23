<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_itineraries', function (Blueprint $table) {
            $table->id();
            $table->integer('trip_request_id');
            $table->string('title')->nullable();
            $table->string('traveler_surname')->nullable();
            $table->string('language')->default('en');
            $table->date('arrival_date')->nullable();
            $table->string('cover_photo')->nullable();
            $table->decimal('price_per_person', 10, 2)->nullable();
            $table->integer('num_travelers')->default(1);
            $table->decimal('group_total', 10, 2)->nullable();
            $table->text('price_includes')->nullable();
            $table->text('price_excludes')->nullable();
            $table->text('booking_conditions')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->timestamps();
            $table->foreign('trip_request_id')->references('id')->on('trip_requests')->onDelete('cascade');
        });

        Schema::create('trip_itinerary_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_itinerary_id');
            $table->integer('day_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('destinations')->nullable();
            $table->boolean('breakfast')->default(false);
            $table->boolean('lunch')->default(false);
            $table->boolean('dinner')->default(false);
            $table->string('accommodation_name')->nullable();
            $table->text('accommodation_description')->nullable();
            $table->string('accommodation_category')->nullable();
            $table->string('accommodation_website')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
            $table->foreign('trip_itinerary_id')->references('id')->on('trip_itineraries')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_itinerary_days');
        Schema::dropIfExists('trip_itineraries');
    }
};
