<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — HotelDesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cream:#faf7f2;--warm:#f0e9de;--stone:#c8bfb0;--muted:#8a7d6e;
            --ink:#2c2416;--gold:#b8913f;--gold-lt:#d4a84b;--rust:#a84b2f;
            --border:#e5ddd0;--shadow-lg:0 8px 40px 0 rgba(44,36,22,.13);--radius:10px;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DM Sans',sans-serif;background:var(--ink);color:var(--ink);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
        .auth-box{background:var(--cream);border-radius:14px;padding:44px 44px 36px;width:100%;max-width:420px;box-shadow:var(--shadow-lg);}
        .auth-logo{font-family:'DM Serif Display',serif;font-size:1.7rem;color:var(--gold);text-align:center;margin-bottom:4px;}
        .auth-sub{text-align:center;color:var(--muted);font-size:.88rem;margin-bottom:32px;}
        .form-group{margin-bottom:18px;}
        .form-label{display:block;font-size:.8rem;font-weight:600;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;margin-bottom:7px;}
        .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;background:#fff;color:var(--ink);font-family:'DM Sans',sans-serif;font-size:.92rem;transition:border-color .15s,box-shadow .15s;outline:none;}
        .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(184,145,63,.15);}
        .form-error{color:var(--rust);font-size:.82rem;margin-top:5px;}
        .btn{display:flex;align-items:center;justify-content:center;width:100%;padding:11px 20px;border-radius:8px;background:var(--gold);color:var(--ink);font-weight:700;font-size:.92rem;border:none;cursor:pointer;transition:background .15s;letter-spacing:.01em;text-decoration:none;}
        .btn:hover{background:var(--gold-lt);}
        .auth-footer{text-align:center;margin-top:22px;font-size:.85rem;color:var(--muted);}
        .auth-footer a{color:var(--gold);text-decoration:none;font-weight:600;}
        .auth-footer a:hover{text-decoration:underline;}
        .divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--stone);font-size:.8rem;}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}
        .remember-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
        .checkbox-label{display:flex;align-items:center;gap:8px;font-size:.87rem;color:var(--muted);cursor:pointer;}
        input[type=checkbox]{accent-color:var(--gold);width:15px;height:15px;}
        .forgot-link{font-size:.85rem;color:var(--gold);text-decoration:none;font-weight:500;}
        .forgot-link:hover{text-decoration:underline;}
        .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:10px 16px;border-radius:8px;font-size:.88rem;margin-bottom:18px;}
    </style>
</head>
<body>
<div class="auth-box">
    <div class="auth-logo">🏨 HotelDesk</div>
    <div class="auth-sub">Sign in to your account</div>

    @if(session('status'))
        <div class="alert-error" style="background:#d1fae5;color:#065f46;border-color:#a7f3d0;">{{ session('status') }}</div>
    @endif

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input id="email" class="form-control" type="email" name="email"
                   value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="you@example.com">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input id="password" class="form-control" type="password" name="password"
                   required autocomplete="current-password" placeholder="••••••••">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="remember-row">
            <label class="checkbox-label">
                <input type="checkbox" name="remember"> Remember me
            </label>
            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn">Sign In</button>
    </form>

    @if (Route::has('register'))
        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </div>
    @endif
</div>
</body>
</html>