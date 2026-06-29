<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->index(['kondisi', 'jenis']);
            $table->index('lokasi');
            $table->index('pic_id');
            $table->index('created_at');
        });

        Schema::table('pics', function (Blueprint $table) {
            $table->index('email');
        });

        Schema::table('asset_histories', function (Blueprint $table) {
            $table->index(['asset_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['kondisi', 'jenis']);
            $table->dropIndex(['lokasi']);
            $table->dropIndex(['pic_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('pics', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });

        Schema::table('asset_histories', function (Blueprint $table) {
            $table->dropIndex(['asset_id', 'created_at']);
        });
    }
};
