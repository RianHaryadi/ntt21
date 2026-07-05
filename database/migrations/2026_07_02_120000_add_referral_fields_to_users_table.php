<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->unique()->after('is_admin');
            $table->foreignId('referred_by_id')->nullable()->after('referral_code')
                ->constrained('users')->nullOnDelete();
        });

        // Backfill kode referral untuk user yang sudah ada sebelum kolom ini ditambahkan
        $existingCodes = [];
        DB::table('users')->whereNull('referral_code')->orderBy('id')->pluck('id')->each(function ($id) use (&$existingCodes) {
            do {
                $code = strtoupper(Str::random(6));
            } while (in_array($code, $existingCodes) || DB::table('users')->where('referral_code', $code)->exists());

            $existingCodes[] = $code;
            DB::table('users')->where('id', $id)->update(['referral_code' => $code]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_id');
            $table->dropColumn('referral_code');
        });
    }
};
