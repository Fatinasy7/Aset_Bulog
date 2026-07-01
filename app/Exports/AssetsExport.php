<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
{
    private Collection $assets;

    public function __construct(Collection $assets)
    {
        $this->assets = $assets;
    }

    public function collection(): Collection
    {
        return $this->assets->map(function ($asset) {
            return [
                'ID' => $asset->id,
                'Kode Aset' => $asset->kode_aset,
                'Nama Aset' => $asset->nama_aset,
                'Merk / Tipe' => $asset->merk_type,
                'Serial Number' => $asset->serial_number,
                'Lokasi' => $asset->lokasi,
                'Latitude' => $asset->koordinat_lat,
                'Longitude' => $asset->koordinat_lng,
                'Kondisi' => $asset->kondisi,
                'Tanggal Perolehan' => optional($asset->tgl_perolehan)->format('Y-m-d'),
                'Harga' => $asset->harga,
                'Jenis' => $asset->jenis,
                'PIC ID' => $asset->pic_id,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kode Aset',
            'Nama Aset',
            'Merk / Tipe',
            'Serial Number',
            'Lokasi',
            'Latitude',
            'Longitude',
            'Kondisi',
            'Tanggal Perolehan',
            'Harga',
            'Jenis',
            'PIC ID',
        ];
    }
}
