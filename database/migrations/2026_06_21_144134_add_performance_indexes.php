<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // booking_hotels: hotel_id & status sudah ada, tambah yang belum
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('booking_number');
        });

        // transactions: booking_code, tour_package_id, destination_id sudah ada
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('status');
        });

        // promotions: code sudah ada
        Schema::table('promotions', function (Blueprint $table) {
            $table->index('active');
            $table->index(['active', 'valid_from', 'valid_until']);
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->index('category');
            $table->index('rating');
            $table->index('status');
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->index('location');
        });

        Schema::table('tour_packages', function (Blueprint $table) {
            $table->index('location');
            $table->index('days');
            $table->index('price');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['booking_number']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropIndex(['active']);
            $table->dropIndex(['active', 'valid_from', 'valid_until']);
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['rating']);
            $table->dropIndex(['status']);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex(['location']);
        });

        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropIndex(['location']);
            $table->dropIndex(['days']);
            $table->dropIndex(['price']);
            $table->dropIndex(['rating']);
        });
    }
};
