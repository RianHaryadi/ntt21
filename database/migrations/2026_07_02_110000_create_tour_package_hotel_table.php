<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_package_hotel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tour_package_id', 'hotel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_hotel');
    }
};
