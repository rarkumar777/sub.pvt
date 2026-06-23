<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_request_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('trip_request_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('sender_type')->default('agent');
            $table->string('sender_name')->nullable();
            $table->text('message');
            $table->timestamps();
            $table->index('trip_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_request_messages');
    }
};
