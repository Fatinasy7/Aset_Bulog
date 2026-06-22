<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pic_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('pic_lama_id')->nullable()->constrained('pics')->nullOnDelete();
            $table->foreignId('pic_baru_id')->nullable()->constrained('pics')->nullOnDelete();
            $table->text('alasan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pic_histories');
    }
};
