<?php

namespace App\Http\Controllers\Traits;

use App\Models\Asset;
use App\Models\Pic;
use App\Models\User;

trait ApiResponseFormatter
{
    protected function snakeToCamel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    protected function toCamelCaseArray(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $camelKey = $this->snakeToCamel($key);

            if (is_array($value)) {
                $value = $this->toCamelCaseArray($value);
            }

            $result[$camelKey] = $value;
        }

        return $result;
    }

    protected function formatPicPayload(Pic $pic): array
    {
        return [
            'id' => $pic->id,
            'nama' => $pic->nama,
            'jabatan' => $pic->jabatan,
            'email' => $pic->email,
            'telepon' => $pic->telepon,
            'createdAt' => $pic->created_at instanceof \DateTimeInterface ? $pic->created_at->format(\DateTimeInterface::ATOM) : null,
            'updatedAt' => $pic->updated_at instanceof \DateTimeInterface ? $pic->updated_at->format(\DateTimeInterface::ATOM) : null,
        ];
    }

    protected function formatUserPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'createdAt' => $user->created_at instanceof \DateTimeInterface ? $user->created_at->format(\DateTimeInterface::ATOM) : null,
            'updatedAt' => $user->updated_at instanceof \DateTimeInterface ? $user->updated_at->format(\DateTimeInterface::ATOM) : null,
        ];
    }

    protected function formatAssetPayload(Asset $asset): array
    {
        return [
            'id' => $asset->id,
            'kodeAset' => $asset->kode_aset,
            'namaAset' => $asset->nama_aset,
            'merkType' => $asset->merk_type,
            'serialNumber' => $asset->serial_number,
            'lokasi' => $asset->lokasi,
            'koordinat' => [
                'lat' => $asset->koordinat_lat,
                'lng' => $asset->koordinat_lng,
            ],
            'kondisi' => $asset->kondisi,
            'tglPerolehan' => $asset->tgl_perolehan instanceof \DateTimeInterface ? $asset->tgl_perolehan->format('Y-m-d') : null,
            'harga' => $asset->harga,
            'keterangan' => $asset->keterangan,
            'jenis' => $asset->jenis,
            'qrCodePath' => $asset->qr_code_path,
            'picId' => $asset->pic_id,
            'pic' => $asset->pic ? $this->formatPicPayload($asset->pic) : null,
            'createdAt' => $asset->created_at instanceof \DateTimeInterface ? $asset->created_at->format(\DateTimeInterface::ATOM) : null,
            'updatedAt' => $asset->updated_at instanceof \DateTimeInterface ? $asset->updated_at->format(\DateTimeInterface::ATOM) : null,
        ];
    }
}
