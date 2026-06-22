<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->string('merk_type');
            $table->string('serial_number')->nullable();
            $table->string('lokasi');
            $table->decimal('koordinat_lat', 10, 7)->nullable();
            $table->decimal('koordinat_lng', 10, 7)->nullable();
            $table->string('kondisi');
            $table->date('tgl_perolehan')->nullable();
            $table->unsignedBigInteger('harga')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('jenis');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
