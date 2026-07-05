<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points'); // positif = didapat, negatif = ditukar
            $table->string('type'); // hotel_booking | tour_booking | referral_bonus | redemption
            $table->string('description');
            $table->string('source_type')->nullable(); // nama model sumber, cth. BookingHotel
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
