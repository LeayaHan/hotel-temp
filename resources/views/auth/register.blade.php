<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — HotelDesk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--cream:#faf7f2;--warm:#f0e9de;--muted:#8a7d6e;--ink:#2c2416;--gold:#b8913f;--gold-lt:#d4a84b;--rust:#a84b2f;--border:#e5ddd0;--shadow-lg:0 8px 40px 0 rgba(44,36,22,.13);}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DM Sans',sans-serif;background:var(--ink);color:var(--ink);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;}
        .auth-box{background:var(--cream);border-radius:14px;padding:44px 44px 36px;width:100%;max-width:440px;box-shadow:var(--shadow-lg);}
        .auth-logo{font-family:'DM Serif Display',serif;font-size:1.7rem;color:var(--gold);text-align:center;margin-bottom:4px;}
        .auth-sub{text-align:center;color:var(--muted);font-size:.88rem;margin-bottom:32px;}
        .form-group{margin-bottom:18px;}
        .form-label{display:block;font-size:.8rem;font-weight:600;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;margin-bottom:7px;}
        .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;background:#fff;color:var(--ink);font-family:'DM Sans',sans-serif;font-size:.92rem;transition:border-color .15s,box-shadow .15s;outline:none;}
        .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(184,145,63,.15);}
        .form-error{color:var(--rust);font-size:.82rem;margin-top:5px;}
        .btn{display:flex;align-items:center;justify-content:center;width:100%;padding:11px 20px;border-radius:8px;background:var(--gold);color:var(--ink);font-weight:700;font-size:.92rem;border:none;cursor:pointer;transition:background .15s;letter-spacing:.01em;}
        .btn:hover{background:var(--gold-lt);}
        .auth-footer{text-align:center;margin-top:22px;font-size:.85rem;color:var(--muted);}
        .auth-footer a{color:var(--gold);text-decoration:none;font-weight:600;}
        .auth-footer a:hover{text-decoration:underline;}
    </style>
</head>
<body>
<div class="auth-box">
    <div class="auth-logo">🏨 HotelDesk</div>
    <div class="auth-sub">Create your guest account</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input id="name" class="form-control" type="text" name="name"
                   value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Juan dela Cruz">
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input id="email" class="form-control" type="email" name="email"
                   value="{{ old('email') }}" required autocomplete="username"
                   placeholder="you@example.com">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input id="password" class="form-control" type="password" name="password"
                   required autocomplete="new-password" placeholder="Min. 8 characters">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" class="form-control" type="password"
                   name="password_confirmation" required autocomplete="new-password"
                   placeholder="Repeat password">
            @error('password_confirmation')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn">Create Account</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
</div>
</body>
</html>