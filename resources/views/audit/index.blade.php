@extends('layouts.app')

@section('title', 'Audit Trail - Frontend BULOG')
@section('topbar-meta', 'Riwayat perubahan aset dan aktivitas update')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/audit-index.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Audit Trail</h1>
        <p class="page-lead">Halaman riwayat perubahan digunakan untuk menampilkan log perubahan data aset, field yang diubah, dan pengguna yang melakukannya.</p>
    </div>
    <div class="content-width-medium">
        <input class="form-control-ui" type="search" placeholder="Cari aset atau pengguna">
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        @if ($logs->isEmpty())
            <div class="empty-state-card">
                <div class="empty-state-card__icon">📝</div>
                <div class="empty-state-card__title">Audit log kosong</div>
                <div class="empty-state-card__message">Belum ada perubahan yang tercatat. Perubahan aset akan muncul setelah ada aktivitas.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table-ui">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Aset</th>
                            <th>Field Berubah</th>
                            <th>Nilai Lama</th>
                            <th>Nilai Baru</th>
                            <th>Diubah Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d') }}</td>
                            <td>{{ $log->asset_code }}</td>
                            <td>{{ $log->field_name ?? $log->action }}</td>
                            <td>{{ $log->old_value ?? '-' }}</td>
                            <td>{{ $log->new_value ?? '-' }}</td>
                            <td>{{ $log->changed_by }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
</section>
@endsection