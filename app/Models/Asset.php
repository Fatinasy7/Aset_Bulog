<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'merk_type',
        'serial_number',
        'lokasi',
        'koordinat_lat',
        'koordinat_lng',
        'kondisi',
        'tgl_perolehan',
        'harga',
        'keterangan',
        'jenis',
        'pic_id',
    ];

    protected $casts = [
        'tgl_perolehan' => 'date',
        'harga' => 'integer',
        'koordinat_lat' => 'double',
        'koordinat_lng' => 'double',
    ];

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
