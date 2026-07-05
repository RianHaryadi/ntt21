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
        Schema::table('hotels', function (Blueprint $table) {
            $table->unsignedTinyInteger('flash_sale_discount_percent')->nullable()->after('family_room_price');
            $table->dateTime('flash_sale_ends_at')->nullable()->after('flash_sale_discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['flash_sale_discount_percent', 'flash_sale_ends_at']);
        });
    }
};
