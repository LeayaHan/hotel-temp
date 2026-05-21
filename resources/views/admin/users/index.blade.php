@extends('layouts.app')

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h1>User Accounts</h1>
        <p>Manage all staff and guest accounts</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn">+ Add Account</a>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="margin-bottom:1rem;">{{ session('error') }}</div>
@endif

{{-- Filters --}}
<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Name or email..."
                   class="form-control">
        </div>

        <div class="form-group" style="margin:0;min-width:160px;">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
                <option value="">All Roles</option>
                <option value="admin"      {{ request('role') === 'admin'      ? 'selected' : '' }}>Administrator</option>
                <option value="manager"    {{ request('role') === 'manager'    ? 'selected' : '' }}>Manager</option>
                <option value="staff"      {{ request('role') === 'staff'      ? 'selected' : '' }}>Housekeeping Staff</option>
                <option value="front_desk" {{ request('role') === 'front_desk' ? 'selected' : '' }}>Front Desk</option>
                <option value="customer"   {{ request('role') === 'customer'   ? 'selected' : '' }}>Guest (Customer)</option>
            </select>
        </div>

        <div class="form-group" style="margin:0;min-width:140px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div style="display:flex;gap:.5rem;">
            <button type="submit" class="btn">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- Users Table --}}
<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:var(--surface);border-bottom:2px solid var(--border);">
                <th style="padding:.75rem 1rem;text-align:left;font-size:.8rem;text-transform:uppercase;color:var(--muted);">Name</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.8rem;text-transform:uppercase;color:var(--muted);">Email</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.8rem;text-transform:uppercase;color:var(--muted);">Role</th>
                <th style="padding:.75rem 1rem;text-align:left;font-size:.8rem;text-transform:uppercase;color:var(--muted);">Department</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.8rem;text-transform:uppercase;color:var(--muted);">Status</th>
                <th style="padding:.75rem 1rem;text-align:center;font-size:.8rem;text-transform:uppercase;color:var(--muted);">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:.75rem 1rem;font-weight:500;">{{ $user->name }}</td>
                <td style="padding:.75rem 1rem;color:var(--muted);font-size:.9rem;">{{ $user->email }}</td>
                <td style="padding:.75rem 1rem;">
                    @php
                        $roleLabels = [
                            'admin'      => ['🔑 Administrator', 'var(--rust)'],
                            'manager'    => ['📋 Manager', 'var(--gold)'],
                            'staff'      => ['🧹 Housekeeping', 'var(--ink)'],
                            'front_desk' => ['🖥 Front Desk', 'var(--ink)'],
                            'customer'   => ['🛎 Guest', 'var(--muted)'],
                        ];
                        [$roleLabel, $roleColor] = $roleLabels[$user->role] ?? [$user->role, 'var(--ink)'];
                    @endphp
                    <span style="font-size:.85rem;color:{{ $roleColor }};">{{ $roleLabel }}</span>
                </td>
                <td style="padding:.75rem 1rem;color:var(--muted);font-size:.9rem;">{{ $user->department ?? '—' }}</td>
                <td style="padding:.75rem 1rem;text-align:center;">
                    @if($user->is_active)
                        <span style="background:#d1fae5;color:#065f46;padding:.2rem .6rem;border-radius:999px;font-size:.78rem;font-weight:600;">Active</span>
                    @else
                        <span style="background:#fee2e2;color:#991b1b;padding:.2rem .6rem;border-radius:999px;font-size:.78rem;font-weight:600;">Inactive</span>
                    @endif
                </td>
                <td style="padding:.75rem 1rem;text-align:center;">
                    <div style="display:flex;gap:.5rem;justify-content:center;">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary" style="padding:.3rem .75rem;font-size:.82rem;">View</a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary" style="padding:.3rem .75rem;font-size:.82rem;">Edit</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Delete account for {{ addslashes($user->name) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="padding:.3rem .75rem;font-size:.82rem;background:var(--rust);">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:2rem;text-align:center;color:var(--muted);">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($users->hasPages())
<div style="margin-top:1rem;">
    {{ $users->links() }}
</div>
@endif

@endsection