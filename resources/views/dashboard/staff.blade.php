@extends('layouts.app')

@section('content')

{{-- ── HEADER ── --}}
<div class="page-header">
    <h1>Welcome back, {{ Auth::user()->name }} 👋</h1>
    <p>Here's your shift overview — manage requests and guests from here.</p>
</div>

{{-- ── STAT CARDS ── --}}
<div class="stats-strip" style="margin-bottom:28px;">
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

{{-- ── QUICK ACTIONS ── --}}
<div class="card" style="margin-bottom:28px;">
    <div class="card-header">
        <h2>Quick Actions</h2>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('service-requests.create') }}" class="btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Request
        </a>
        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            All Requests
        </a>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Manage Guests
        </a>
    </div>
</div>

{{-- ── GUESTS OVERVIEW ── --}}
<div class="card-header" style="margin-bottom:14px;">
    <div>
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--ink);margin:0;">Current Guests</h2>
        <p style="color:var(--muted);font-size:.88rem;margin-top:2px;">All registered guests and their active request counts.</p>
    </div>
    <a href="{{ route('guests.index') }}" class="btn btn-secondary btn-sm">View All →</a>
</div>

@if($guests->isEmpty())
    <div class="card" style="margin-bottom:28px;">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <h3>No guests yet</h3>
            <p>Registered guests will appear here.</p>
        </div>
    </div>
@else
    <div class="table-wrap" style="margin-bottom:28px;">
        <table>
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Active Requests</th>
                    <th>Total Requests</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guests as $guest)
                <tr>
                    <td>
                        <strong>{{ $guest->full_name }}</strong><br>
                        <span style="color:var(--muted);font-size:.82rem;">{{ $guest->email ?? '—' }}</span>
                    </td>
                    <td>{{ $guest->room_number ?? '—' }}</td>
                    <td style="font-size:.85rem;white-space:nowrap;">
                        {{ $guest->check_in ? \Carbon\Carbon::parse($guest->check_in)->format('M d, Y') : '—' }}
                    </td>
                    <td style="font-size:.85rem;white-space:nowrap;">
                        {{ $guest->check_out ? \Carbon\Carbon::parse($guest->check_out)->format('M d, Y') : '—' }}
                    </td>
                    <td>
                        @php
                            $gs = $guest->status ?? '';
                            $gcls = match($gs) {
                                'Checked In'  => 'badge-done',
                                'Checked Out' => 'badge-cancelled',
                                'Reserved'    => 'badge-progress',
                                default       => 'badge-normal',
                            };
                        @endphp
                        <span class="badge {{ $gcls }}">{{ $gs ?: '—' }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if($guest->open_requests > 0)
                            <span class="badge badge-progress">{{ $guest->open_requests }}</span>
                        @else
                            <span style="color:var(--muted);font-size:.85rem;">0</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span style="font-weight:600;">{{ $guest->total_requests }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- ── PENDING REQUESTS TABLE ── --}}
<div class="card-header" style="margin-bottom:14px;">
    <div>
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--ink);margin:0;">Pending & In-Progress Requests</h2>
        <p style="color:var(--muted);font-size:.88rem;margin-top:2px;">Open requests that need your attention right now.</p>
    </div>
    <a href="{{ route('service-requests.index') }}" class="btn btn-secondary btn-sm">View All →</a>
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
                    <td>
                        <strong>{{ $req->guest->full_name ?? '—' }}</strong><br>
                        <span style="color:var(--muted);font-size:.82rem;">Room {{ $req->guest->room_number ?? '—' }}</span>
                    </td>
                    <td>{{ $req->service_type }}</td>
                    <td>
                        @php $p = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
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
                        <span class="badge {{ $cls }}">{{ $s }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">
                        {{ $req->created_at->format('M d, Y g:i A') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('service-requests.edit', $req) }}" class="btn btn-sm">Update</a>
                            <a href="{{ route('service-requests.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection