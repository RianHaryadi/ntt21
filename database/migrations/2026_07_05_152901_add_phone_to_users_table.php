<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
        });

        $this->backfillFromTable('transactions');
        $this->backfillFromTable('booking_hotels');
        $this->backfillFromTable('tour_bookings');
        $this->backfillFromTable('orders');
    }

    private function backfillFromTable(string $table): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'user_id') || !Schema::hasColumn($table, 'customer_phone')) {
            return;
        }

        DB::table($table)
            ->whereNotNull('user_id')
            ->whereNotNull('customer_phone')
            ->orderByDesc('created_at')
            ->get(['user_id', 'customer_phone'])
            ->unique('user_id')
            ->each(function ($row) {
                DB::table('users')
                    ->where('id', $row->user_id)
                    ->whereNull('phone')
                    ->update(['phone' => $row->customer_phone]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
