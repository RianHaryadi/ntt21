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
        Schema::table('reviews', function (Blueprint $table) {
            $table->tinyInteger('cleanliness_rating')->nullable()->after('rating');
            $table->tinyInteger('location_rating')->nullable()->after('cleanliness_rating');
            $table->tinyInteger('value_rating')->nullable()->after('location_rating');
            $table->tinyInteger('service_rating')->nullable()->after('value_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['cleanliness_rating', 'location_rating', 'value_rating', 'service_rating']);
        });
    }
};
