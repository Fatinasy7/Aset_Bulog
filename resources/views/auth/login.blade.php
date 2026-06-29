@extends('layouts.auth')

@section('title', 'Login - Sistem Manajemen Aset BULOG')

@section('content')
<section class="auth-card">
    <div class="auth-card__hero auth-card__hero--brand">
        <div class="brand-icon">LA</div>
        <div class="auth-heading">
            <p class="brand-title">Lumina Asset</p>
            <p class="brand-subtitle">Enterprise Portal</p>
        </div>
    </div>

    <div class="auth-card__body stack">
        <p class="auth-intro">Masuk dengan akun resmi untuk mengelola aset, laporan, dan scan QR secara cepat.</p>

        <form class="stack" action="#" method="post">
            @csrf
            <div>
                <label class="form-label-ui" for="email">Work Email</label>
                <input class="form-control-ui" type="email" id="email" name="email" placeholder="name@company.com" required>
            </div>

            <div>
                <div class="form-label-row">
                    <label class="form-label-ui" for="password">Password</label>
                    <a class="link-button" href="#">Forgot?</a>
                </div>
                <input class="form-control-ui" type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <label class="checkbox-ui">
                <input type="checkbox" name="remember">
                <span>Remember this terminal for 30 days</span>
            </label>

            <button class="btn-ui btn-primary-ui btn-full" type="submit">Authenticate <span aria-hidden="true">→</span></button>
        </form>

        <div class="auth-divider"><span>OR SIGN IN WITH</span></div>
        <div class="auth-socials">
            <button class="btn-ui btn-secondary-ui btn-block">SSO</button>
            <button class="btn-ui btn-secondary-ui btn-block">Passkey</button>
        </div>

        <p class="surface-note auth-footer">Authorized access only. <a href="#">Contact Admin</a></p>
    </div>
</section>
@endsection