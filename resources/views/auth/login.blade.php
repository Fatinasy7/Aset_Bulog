@extends('layouts.auth')

@section('title', 'Login - Aset Bulog')

@section('content')
<section class="auth-shell__panel">
    <div class="auth-card auth-card--login">
        <div class="auth-card__top">
            <div class="auth-card__icon">&lt;/&gt;</div>
            <div class="auth-card__brand">
                <h1 class="auth-card__title">Aset <span>Bulog</span></h1>
                <p class="auth-card__subtitle">Enterprise Portal</p>
            </div>
        </div>

        <form class="auth-form" action="{{ route('frontend.login.submit') }}" method="POST" novalidate>
            @csrf

            @if ($errors->any())
                <div class="alert-ui alert-danger">
                    <strong>Login gagal.</strong>
                    <ul class="list-unstyled mt-2 mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="field">
                <label class="form-label-ui" for="email">@ Work Email</label>
                <input class="form-control-ui auth-input {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autofocus>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <div class="form-label-row">
                    <label class="form-label-ui" for="password">🔒 Password</label>
                    <a class="link-button" href="#">Forgot?</a>
                </div>
                <input class="form-control-ui auth-input {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" id="password" name="password" placeholder="••••••••" required>
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <label class="checkbox-ui">
                <input type="checkbox" name="remember">
                <span>Remember this terminal for 30 days</span>
            </label>

            <button class="btn-ui btn-primary-ui btn-full auth-submit" type="submit">Authenticate <span aria-hidden="true">→</span></button>
        </form>

        <div class="auth-divider"><span>OR SIGN IN WITH</span></div>

        <div class="auth-socials">
            <button class="btn-ui btn-secondary-ui btn-block auth-social-btn" type="button">SSO</button>
            <button class="btn-ui btn-secondary-ui btn-block auth-social-btn" type="button">Passkey</button>
        </div>

        <p class="surface-note auth-footer">Authorized access only. <a href="#">Contact Admin</a></p>
    </div>
</section>
@endsection