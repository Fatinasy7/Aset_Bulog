<x-mail::message>
# Laporan Kerusakan Aset

Aset **{{ $asset->kode_aset }}** telah dilaporkan berubah kondisi.

- Nama Aset: {{ $asset->nama_aset }}
- Lokasi: {{ $asset->lokasi }}
- Kondisi Sebelumnya: {{ $oldCondition ?? 'Tidak tersedia' }}
- Kondisi Baru: {{ $newCondition }}

Silakan buka sistem untuk memeriksa detail aset dan menindaklanjuti perbaikan jika diperlukan.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
