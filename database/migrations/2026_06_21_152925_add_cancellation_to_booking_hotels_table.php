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
            $table->string('cancellation_status')->nullable()->after('status');
            $table->text('cancellation_reason')->nullable()->after('cancellation_status');
            $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_reason');
            $table->timestamp('cancellation_processed_at')->nullable()->after('cancellation_requested_at');
            $table->index('cancellation_status');
        });
    }

    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->dropIndex(['cancellation_status']);
            $table->dropColumn(['cancellation_status', 'cancellation_reason', 'cancellation_requested_at', 'cancellation_processed_at']);
        });
    }
};
