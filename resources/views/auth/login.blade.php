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

        <div class="auth-card__body">
            <form class="auth-form" action="{{ route('frontend.dashboard') }}" method="get">
                <div class="field">
                    <label class="form-label-ui" for="email">@ Work Email</label>
                    <input class="form-control-ui auth-input" type="email" id="email" name="email" placeholder="name@company.com" required>
                </div>

                <div class="field">
                    <div class="form-label-row">
                        <label class="form-label-ui" for="password">🔒 Password</label>
                        <a class="link-button" href="#">Forgot?</a>
                    </div>
                    <input class="form-control-ui auth-input" type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="auth-form__footer">
                    <label class="checkbox-ui">
                        <input type="checkbox" name="remember">
                        <span>Remember this terminal for 30 days</span>
                    </label>

                    <button class="btn-ui btn-primary-ui btn-full auth-submit" type="submit">Authenticate <span aria-hidden="true">→</span></button>
                </div>
            </form>

            <div class="auth-divider"><span>OR SIGN IN WITH</span></div>

            <div class="auth-socials">
                <button class="btn-ui btn-secondary-ui btn-block auth-social-btn" type="button">SSO</button>
                <button class="btn-ui btn-secondary-ui btn-block auth-social-btn" type="button">Passkey</button>
            </div>

            <p class="surface-note auth-footer">Authorized access only. <a href="#">Contact Admin</a></p>
        </div>
    </div>
</section>
@endsection