@extends('layouts.app')

@section('title', 'Preview Semua - Lumina Asset')
@section('topbar-meta', 'Tampilkan semua halaman frontend dalam satu tampilan')

@push('styles')
    @vite(['resources/css/frontend-preview.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Preview Semua Halaman</h1>
        <p class="page-lead">Seluruh tampilan frontend bisa dilihat dalam satu halaman untuk review cepat.</p>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>1. Login</strong>
    </div>
    <div class="card-surface__body">
        <a href="{{ route('frontend.login') }}" class="link-card">Buka tampilan Login</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>2. Dashboard</strong>
    </div>
    <div class="card-surface__body">
        <a href="{{ route('frontend.dashboard') }}" class="link-card">Buka Dashboard</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>3. Data Laptop</strong>
    </div>
    <div class="card-surface__body">
        <a href="{{ route('frontend.assets.laptops') }}" class="link-card">Buka Data Laptop</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>4. Data Printer</strong>
    </div>
    <div class="card-surface__body">
        <a href="{{ route('frontend.assets.printers') }}" class="link-card">Buka Data Printer</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>5. Scan QR</strong>
    </div>
    <div class="card-surface__body">
        <a href="{{ route('frontend.scan-qr') }}" class="link-card">Buka Scan QR</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>6. Laporan</strong>
    </div>
    <div class="card-surface__body">
        <a href="{{ route('frontend.reports.index') }}" class="link-card">Buka Laporan</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>7. Pengaturan</strong>
    </div>
    <div class="card-surface__body">
        <a href="{{ route('frontend.settings') }}" class="link-card">Buka Pengaturan</a>
    </div>
</section>
@endsection
