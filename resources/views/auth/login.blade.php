@extends('layouts.auth')

@section('title', 'Login - Sistem Manajemen Aset BULOG')

@section('content')
<section class="auth-card">
    <div class="auth-card__hero">
        <div class="brand-block">
            <div class="brand-mark" aria-hidden="true">
                <span>BA</span>
            </div>
            <div>
                <p class="brand-title">Masuk ke Sistem</p>
                <p class="brand-subtitle">Preview layout login untuk frontend Wahyu</p>
            </div>
        </div>
    </div>

    <div class="auth-card__body stack">
        <div class="alert-ui alert-info-ui">
            Gunakan halaman ini sebagai acuan layout login sebelum koneksi autentikasi backend dipasang.
        </div>

        <form class="stack" action="#" method="post">
            @csrf
            <div>
                <label class="form-label-ui" for="email">Email</label>
                <input class="form-control-ui" type="email" id="email" name="email" placeholder="nama@bulog.co.id" required>
            </div>

            <div>
                <label class="form-label-ui" for="password">Password</label>
                <input class="form-control-ui" type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <div>
                <label class="form-label-ui" for="role">Role</label>
                <select class="form-select-ui" id="role" name="role" required>
                    <option value="">Pilih role</option>
                    <option value="admin">Admin IT</option>
                    <option value="pic">User / PIC</option>
                    <option value="manajemen">Manajemen</option>
                </select>
            </div>

            <button class="btn-ui btn-primary-ui" type="submit">Masuk</button>
        </form>

        <p class="surface-note">Layout ini sudah responsif dan siap dipakai sebagai pondasi untuk login final.</p>
    </div>
</section>
@endsection