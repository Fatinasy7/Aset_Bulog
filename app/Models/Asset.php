<?php

namespace App\Models;

use App\Models\AssetHistory;
use App\Models\Pic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

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
        'qr_code_path',
        'pic_id',
        'pic_name',
    ];

    protected $casts = [
        'tgl_perolehan' => 'date',
        'harga' => 'integer',
        'koordinat_lat' => 'double',
        'koordinat_lng' => 'double',
    ];

    public function histories()
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function pic()
    {
        return $this->belongsTo(Pic::class);
    }
}
