@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Edit User</h1>
    <p>Update details for {{ $user->name }}.</p>
</div>

<div class="card" style="max-width:560px;">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label" for="name">Full Name <span style="color:var(--rust)">*</span></label>
            <input id="name" name="name" type="text" class="form-control"
                   value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email <span style="color:var(--rust)">*</span></label>
            <input id="email" name="email" type="email" class="form-control"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="role">Role <span style="color:var(--rust)">*</span></label>
            <select id="role" name="role" class="form-control" required>
                <option value="guest" {{ old('role', $user->role) == 'guest' ? 'selected' : '' }}>Guest</option>
                <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label" for="password">New Password <span style="color:var(--muted);font-weight:400;text-transform:none;">(leave blank to keep)</span></label>
                <input id="password" name="password" type="password" class="form-control" placeholder="Min. 8 characters">
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Repeat password">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save Changes</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection