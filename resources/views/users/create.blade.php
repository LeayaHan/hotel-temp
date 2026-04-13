@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Add User</h1>
    <p>Create a new system user and assign their role.</p>
</div>

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Full Name <span style="color:var(--rust)">*</span></label>
            <input id="name" name="name" type="text" class="form-control"
                   value="{{ old('name') }}" required placeholder="Juan dela Cruz">
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email <span style="color:var(--rust)">*</span></label>
            <input id="email" name="email" type="email" class="form-control"
                   value="{{ old('email') }}" required placeholder="user@example.com">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="role">Role <span style="color:var(--rust)">*</span></label>
            <select id="role" name="role" class="form-control" required>
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select a role…</option>
                <option value="guest" {{ old('role') == 'guest' ? 'selected' : '' }}>Guest</option>
                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label" for="password">Password <span style="color:var(--rust)">*</span></label>
                <input id="password" name="password" type="password" class="form-control"
                       required placeholder="Min. 8 characters">
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="form-control" required placeholder="Repeat password">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20,6 9,17 4,12"/></svg>
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection