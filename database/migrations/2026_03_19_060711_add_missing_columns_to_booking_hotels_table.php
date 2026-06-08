<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            // Add promo_code_id column if missing
            if (!Schema::hasColumn('booking_hotels', 'promo_code_id')) {
                $table->unsignedBigInteger('promo_code_id')->nullable()->after('promo_code');
            }

            // Add special_requests column if missing
            if (!Schema::hasColumn('booking_hotels', 'special_requests')) {
                $table->text('special_requests')->nullable()->after('payment_method');
            }

            // Fix status enum: add confirmed and canceled values
            // We'll change the enum to support all needed values
            \DB::statement("ALTER TABLE booking_hotels MODIFY COLUMN status ENUM('pending','confirmed','canceled','checked-in','checked-out','failed') DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            if (Schema::hasColumn('booking_hotels', 'promo_code_id')) {
                $table->dropColumn('promo_code_id');
            }
            if (Schema::hasColumn('booking_hotels', 'special_requests')) {
                $table->dropColumn('special_requests');
            }
            \DB::statement("ALTER TABLE booking_hotels MODIFY COLUMN status ENUM('pending','checked-in','checked-out','failed') DEFAULT 'pending'");
        });
    }
};
