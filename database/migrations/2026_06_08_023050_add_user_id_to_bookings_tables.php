<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        if (Schema::hasTable('tour_bookings')) {
            Schema::table('tour_bookings', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropColumn('user_id');
        });

        if (Schema::hasTable('tour_bookings')) {
            Schema::table('tour_bookings', function (Blueprint $table) {
                $table->dropForeignIdFor(\App\Models\User::class);
                $table->dropColumn('user_id');
            });
        }
    }
};
