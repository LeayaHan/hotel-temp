@extends('layouts.app')

@section('content')

{{-- ── HEADER ── --}}
<div class="page-header">
    <h1>Admin Dashboard 🛠</h1>
    <p>Full system overview — manage guests, service requests, and staff accounts.</p>
</div>

{{-- ── STAT CARDS ── --}}
<div class="stats-strip" style="margin-bottom:28px;">
    <div class="stat-card highlight">
        <div class="stat-label">Total Guests</div>
        <div class="stat-value">{{ $totalGuests }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Checked In</div>
        <div class="stat-value">{{ $checkedIn }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Open Requests</div>
        <div class="stat-value">{{ $openRequests }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed Today</div>
        <div class="stat-value">{{ $completedToday }}</div>
    </div>
</div>

{{-- ── QUICK ACTIONS ── --}}
<div class="card" style="margin-bottom:28px;">
    <div class="card-header">
        <h2>Quick Actions</h2>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('admin.users.create') }}" class="btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add User
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            Manage Users
        </a>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Manage Guests
        </a>
        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            All Requests
        </a>
    </div>
</div>

{{-- ── TWO COLUMN GRID ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:28px;">

    {{-- Recent Service Requests --}}
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div>
                <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0;">Recent Requests</h2>
                <p style="color:var(--muted);font-size:.83rem;margin-top:2px;">Latest 5 service requests across all guests.</p>
            </div>
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary btn-sm">View All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests as $req)
                    <tr>
                        <td style="font-size:.87rem;">{{ $req->guest->full_name ?? '—' }}</td>
                        <td style="font-size:.87rem;">{{ $req->service_type }}</td>
                        <td>
                            @php
                                $s = $req->status ?? 'Open';
                                $cls = match($s) {
                                    'Open'        => 'badge-open',
                                    'In Progress' => 'badge-progress',
                                    'Completed'   => 'badge-done',
                                    'Cancelled'   => 'badge-cancelled',
                                    default       => 'badge-normal'
                                };
                            @endphp
                            <span class="badge {{ $cls }}" style="font-size:.72rem;">{{ $s }}</span>
                        </td>
                        <td>
                            <a href="{{ route('service-requests.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--muted);padding:24px;">No requests yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Staff & Users Overview --}}
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div>
                <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0;">Staff & Users</h2>
                <p style="color:var(--muted);font-size:.83rem;margin-top:2px;">Most recently added accounts.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">View All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <div class="nav-avatar {{ $u->role }}" style="width:28px;height:28px;font-size:.8rem;flex-shrink:0;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <span style="font-size:.87rem;">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $u->role }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td>
                            @if($u->is_active)
                                <span class="badge badge-done" style="font-size:.72rem;">Active</span>
                            @else
                                <span class="badge badge-cancelled" style="font-size:.72rem;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-secondary btn-sm">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--muted);padding:24px;">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── USER ROLE BREAKDOWN ── --}}
<div class="card-header" style="margin-bottom:14px;">
    <div>
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0;">User Role Breakdown</h2>
        <p style="color:var(--muted);font-size:.83rem;margin-top:2px;">Current count of accounts by role.</p>
    </div>
</div>
<div class="stats-strip" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card" style="border-left:4px solid var(--admin-accent);">
        <div class="stat-label">Admins</div>
        <div class="stat-value" style="color:var(--admin-accent);">{{ $adminCount }}</div>
    </div>
    <div class="stat-card" style="border-left:4px solid var(--staff-accent);">
        <div class="stat-label">Staff</div>
        <div class="stat-value" style="color:var(--staff-accent);">{{ $staffCount }}</div>
    </div>
    <div class="stat-card" style="border-left:4px solid var(--gold);">
        <div class="stat-label">Guests (Accounts)</div>
        <div class="stat-value" style="color:var(--gold);">{{ $guestCount }}</div>
    </div>
</div>

@endsection