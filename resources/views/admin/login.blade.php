<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <title>Login — Admin Panel</title>
    <link rel="stylesheet" href="{{ base_url('assets/css/style-admin.css?v=0.0.1') }}">
    <link rel="stylesheet" href="{{ url('css/site.css?v=1') }}">
    <link rel="stylesheet" href="{{ url('css/admin.css?v=1') }}">
    @if (! config('beranda.disable_recaptcha'))
        <script src='https://www.google.com/recaptcha/api.js'></script>
    @endif
</head>
<body class="admin">
    <main class="auth">
        <div class="auth__card">
            <div class="auth__head">
                <img src="{{ base_url('berandahukum.svg') }}" alt="Beranda Hukum">
                <span class="kicker">Admin Panel</span>
            </div>

            @if (session('msg_flash'))
                {!! session('msg_flash') !!}
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="post" action="" autocomplete="off">
                @csrf
                <div class="field">
                    <label for="loginName">Email Login</label>
                    <input type="text" id="loginName" name="loginName" value="{{ old('loginName') }}" required autofocus>
                </div>
                <div class="field">
                    <label for="loginPassword">Katasandi</label>
                    <input type="password" id="loginPassword" name="loginPassword" required>
                </div>
                @if (! config('beranda.disable_recaptcha'))
                    <div class="field"><div class="g-recaptcha" data-sitekey="{{ $google_key }}"></div></div>
                @endif
                <button class="btn btn--primary btn--block" type="submit" name="btn_login">Login</button>
                <p class="small muted" style="margin-top:var(--s4);text-align:center;"><a href="{{ site_url('forget') }}">Lupa password?</a></p>
            </form>
        </div>
    </main>
    <style>
        .auth { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: var(--s5); background: var(--n-50); }
        .auth__card { width: 100%; max-width: 400px; background: var(--n-0); border: 1px solid var(--n-200); border-top: 4px solid var(--brand); padding: var(--s6); }
        .auth__head { display: flex; flex-direction: column; gap: var(--s2); margin-bottom: var(--s5); }
        .auth__head img { height: 36px; width: auto; }
    </style>
</body>
</html>
