<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_package_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->string('name');                                  // mis. "Open Trip", "Private Trip"
            $table->string('price_type')->default('per_person');     // per_person | flat (harga total rombongan)
            $table->decimal('price', 12, 2);
            $table->unsignedSmallInteger('min_pax')->default(1);
            $table->unsignedSmallInteger('max_pax')->nullable();     // null = tanpa batas
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_variants');
    }
};
