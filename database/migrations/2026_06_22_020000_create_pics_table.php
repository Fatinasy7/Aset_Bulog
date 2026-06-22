<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pics', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('email')->unique();
            $table->string('telepon')->nullable();
            $table->timestamps();
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('pic_id')->nullable()->constrained('pics')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->dropColumn('pic_id');
        });

        Schema::dropIfExists('pics');
    }
};
