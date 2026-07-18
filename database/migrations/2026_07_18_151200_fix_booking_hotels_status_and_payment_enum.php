<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbaiki ENUM status booking_hotels:
     * - Tambah 'cancelled' (konsisten dengan kode PHP, sebelumnya hanya ada 'canceled' di migration lama)
     * - Hapus 'failed' (diganti 'cancelled')
     * - Pastikan 'confirmed' ada
     *
     * Perbaiki ENUM payment_method:
     * - Hapus 'pending' dari payment_method (payment_method null = belum bayar, bukan enum 'pending')
     */
    public function up(): void
    {
        // Fix status enum — tambah 'cancelled' (2 L), pertahankan yang lama untuk backward compat
        DB::statement("ALTER TABLE booking_hotels MODIFY COLUMN status 
            ENUM('pending','confirmed','checked-in','checked-out','cancelled','canceled','failed') 
            DEFAULT 'pending'");

        // Fix payment_method — hapus 'pending' dari enum, ganti jadi nullable
        // Step 1: ubah kolom ke nullable varchar sementara agar bisa SET NULL
        DB::statement("ALTER TABLE booking_hotels MODIFY COLUMN payment_method VARCHAR(20) NULL DEFAULT NULL");

        // Step 2: update existing rows yang masih 'pending' menjadi NULL
        DB::statement("UPDATE booking_hotels SET payment_method = NULL WHERE payment_method = 'pending'");

        // Step 3: ubah ke enum final tanpa 'pending'
        DB::statement("ALTER TABLE booking_hotels MODIFY COLUMN payment_method 
            ENUM('transfer','qris','cash') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE booking_hotels MODIFY COLUMN status 
            ENUM('pending','confirmed','canceled','checked-in','checked-out','failed') 
            DEFAULT 'pending'");

        DB::statement("ALTER TABLE booking_hotels MODIFY COLUMN payment_method 
            ENUM('pending','transfer','qris','cash') DEFAULT 'pending'");
    }
};
