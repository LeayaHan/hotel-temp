@extends('layouts.app')

@section('content')
@php $role = Auth::user()->role ?? 'guest'; @endphp

{{-- ── GUEST DASHBOARD ── --}}
@if($role === 'guest')

<div class="card">
    <div class="page-header" style="margin-bottom:0">
        <h1>Welcome, {{ Auth::user()->name }} 👋</h1>
        <p>Need something during your stay? Submit a service request and our team will take care of it.</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
        <a href="{{ route('my-services.create') }}" class="btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Service Request
        </a>
        <a href="{{ route('my-services.index') }}" class="btn btn-secondary">View All Requests</a>
    </div>
</div>

<div class="page-header" style="margin-bottom:16px;">
    <h2 style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--ink);">Recent Requests</h2>
    <p>Your 5 most recent service requests.</p>
</div>

@if($recentRequests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <h3>No requests yet</h3>
            <p style="margin-bottom:20px;">Submit your first request and our team will attend to you shortly.</p>
            <a href="{{ route('my-services.create') }}" class="btn" style="display:inline-flex;">Make a Request</a>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Details</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentRequests as $req)
                <tr>
                    <td><strong>{{ $req->service_type }}</strong></td>
                    <td style="max-width:200px;color:var(--muted);font-size:.87rem;">{{ $req->details ? Str::limit($req->details,60) : '—' }}</td>
                    <td>
                        @php $p = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php $s=$req->status??'Open';$cls=match($s){'Open'=>'badge-open','In Progress'=>'badge-progress','Completed'=>'badge-done','Cancelled'=>'badge-cancelled',default=>'badge-normal'}; @endphp
                        <span class="badge {{ $cls }}">{{ $s }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">{{ $req->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('my-services.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                            @if($req->status === 'Open')
                                <a href="{{ route('my-services.edit', $req) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('my-services.destroy', $req) }}" onsubmit="return confirm('Cancel this request?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($recentRequests->count() === 5)
        <div style="margin-top:14px;">
            <a href="{{ route('my-services.index') }}" class="btn btn-secondary btn-sm">View all requests →</a>
        </div>
    @endif
@endif

{{-- ── STAFF DASHBOARD ── --}}
@elseif($role === 'staff')

<div class="stats-strip">
    <div class="stat-card highlight">
        <div class="stat-label">Open Requests</div>
        <div class="stat-value">{{ $openRequests }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">In Progress</div>
        <div class="stat-value">{{ $inProgress }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed Today</div>
        <div class="stat-value">{{ $completedToday }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Guests</div>
        <div class="stat-value">{{ $totalGuests }}</div>
    </div>
</div>

<div class="card">
    <div class="page-header" style="margin-bottom:0">
        <h1>Welcome back, {{ Auth::user()->name }} 👋</h1>
        <p>Here's an overview of pending service requests assigned to your team.</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
        <a href="{{ route('service-requests.index') }}" class="btn">View All Requests</a>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">Manage Guests</a>
    </div>
</div>

<div class="page-header" style="margin-bottom:16px;">
    <h2 style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--ink);">Pending & In-Progress Requests</h2>
    <p>Most recent open requests that need attention.</p>
</div>

@if($pendingRequests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="20,6 9,17 4,12"/></svg>
            <h3>All caught up!</h3>
            <p>No open or in-progress requests at the moment.</p>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Service Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingRequests as $req)
                <tr>
                    <td><strong>{{ $req->guest->full_name ?? '—' }}</strong><br><span style="color:var(--muted);font-size:.82rem;">Room {{ $req->guest->room_number ?? '—' }}</span></td>
                    <td>{{ $req->service_type }}</td>
                    <td>
                        @php $p=strtolower($req->priority??'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php $s=$req->status??'Open';$cls=match($s){'Open'=>'badge-open','In Progress'=>'badge-progress','Completed'=>'badge-done','Cancelled'=>'badge-cancelled',default=>'badge-normal'}; @endphp
                        <span class="badge {{ $cls }}">{{ $s }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">{{ $req->created_at->format('M d, Y g:i A') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('service-requests.edit', $req) }}" class="btn btn-sm">Update Status</a>
                            <a href="{{ route('service-requests.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- ── ADMIN DASHBOARD ── --}}
@elseif($role === 'admin')

<div class="stats-strip">
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

<div class="card">
    <div class="page-header" style="margin-bottom:0">
        <h1>Admin Dashboard 🛠</h1>
        <p>Full system overview — manage guests, requests, and staff users.</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
        <a href="{{ route('admin.users.index') }}" class="btn">Manage Users</a>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">Manage Guests</a>
        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">All Requests</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
    {{-- Recent Requests --}}
    <div>
        <div class="page-header" style="margin-bottom:12px;">
            <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);">Recent Requests</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Guest</th><th>Type</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($recentRequests as $req)
                    <tr>
                        <td style="font-size:.87rem;">{{ $req->guest->full_name ?? '—' }}</td>
                        <td style="font-size:.87rem;">{{ $req->service_type }}</td>
                        <td>
                            @php $s=$req->status??'Open';$cls=match($s){'Open'=>'badge-open','In Progress'=>'badge-progress','Completed'=>'badge-done','Cancelled'=>'badge-cancelled',default=>'badge-normal'}; @endphp
                            <span class="badge {{ $cls }}" style="font-size:.72rem;">{{ $s }}</span>
                        </td>
                        <td><a href="{{ route('service-requests.show',$req) }}" class="btn btn-secondary btn-sm">View</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:20px;">No requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Staff/Users Overview --}}
    <div>
        <div class="page-header" style="margin-bottom:12px;">
            <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);">Staff & Users</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Name</th><th>Role</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $u)
                    <tr>
                        <td style="font-size:.87rem;">{{ $u->name }}</td>
                        <td><span class="badge badge-{{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                        <td><a href="{{ route('admin.users.edit',$u) }}" class="btn btn-secondary btn-sm">Edit</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px;">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif

@endsection