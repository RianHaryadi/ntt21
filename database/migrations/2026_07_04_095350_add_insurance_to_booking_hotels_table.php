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
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->boolean('has_insurance')->default(false)->after('discount_amount');
            $table->decimal('insurance_amount', 12, 2)->default(0)->after('has_insurance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->dropColumn(['has_insurance', 'insurance_amount']);
        });
    }
};
