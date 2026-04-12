<x-guest-layout>
    <div style="max-width:420px; margin:40px auto; background:#fffaf3; padding:30px; border-radius:14px; box-shadow:0 4px 14px rgba(0,0,0,0.08);">
        <h2 style="text-align:center; color:#5c4033; margin-bottom:20px;">TaskInn Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       style="width:100%; padding:10px; border:1px solid #ccb8a0; border-radius:8px; margin-top:6px; margin-bottom:14px;">
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" required
                       style="width:100%; padding:10px; border:1px solid #ccb8a0; border-radius:8px; margin-top:6px; margin-bottom:14px;">
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="{{ route('register') }}" style="color:#a67c52;">Sign up</a>
            </div>

            <button type="submit"
                    style="width:100%; background:#a67c52; color:white; border:none; padding:12px; border-radius:8px; cursor:pointer;">
                Login
            </button>
        </form>
    </div>
</x-guest-layout>