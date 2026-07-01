<x-mail::message>
# Pengingat Pemeriksaan Aset

Halo {{ $pic->nama }},

Berikut adalah aset yang perlu diperiksa hari ini:

@foreach($assets as $asset)
- **{{ $asset->kode_aset }}** - {{ $asset->nama_aset }} ({{ $asset->lokasi }})
@endforeach

Silakan lakukan pemeriksaan dan update status aset di sistem.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
