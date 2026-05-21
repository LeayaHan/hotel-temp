@extends('layouts.app')

@section('content')
@php $role = Auth::user()->role ?? 'guest'; @endphp

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- GUEST / CUSTOMER DASHBOARD                                   --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($role === 'customer' || $role === 'guest')

<div class="card">
    <div class="page-header" style="margin-bottom:0">
        <h1>Welcome, {{ Auth::user()->name }} 👋</h1>
        <p>Need something during your stay? Submit a service request and our team will take care of it.</p>
    </div>
    @if(isset($guest) && $guest)
        <div style="margin-top:16px;padding:12px 16px;background:var(--warm);border-radius:8px;font-size:.88rem;color:var(--muted);">
            🛏 Room <strong style="color:var(--ink);">{{ $guest->room_number ?? '—' }}</strong>
            &nbsp;·&nbsp; Floor <strong style="color:var(--ink);">{{ $guest->floor_number ?? '—' }}</strong>
            &nbsp;·&nbsp; Status <strong style="color:var(--ink);">{{ $guest->status ?? '—' }}</strong>
        </div>
    @endif
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:20px;">
        <a href="{{ route('my-services.create') }}" class="btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Service Request
        </a>
        <a href="{{ route('my-services.index') }}" class="btn btn-secondary">View All Requests</a>
    </div>
</div>

{{-- Tab Navigation --}}
<div style="display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:0;">
    <button onclick="showTab('active')" data-tab="active"
        style="padding:9px 20px;border:none;background:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;color:var(--gold);border-bottom:2px solid var(--gold);margin-bottom:-2px;">
        Active Requests
    </button>
    <button onclick="showTab('history')" data-tab="history"
        style="padding:9px 20px;border:none;background:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;color:var(--muted);border-bottom:2px solid transparent;margin-bottom:-2px;">
        History
    </button>
</div>

{{-- Active Requests Tab --}}
<div data-panel="active">
    <div class="page-header" style="margin-bottom:16px;">
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);">Active Requests</h2>
        <p style="font-size:.85rem;color:var(--muted);">Open and in-progress requests.</p>
    </div>

    @if(isset($recentRequests) && $recentRequests->isEmpty())
        <div class="card">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                <h3>No active requests</h3>
                <p style="margin-bottom:20px;">Submit a request and our team will attend to you shortly.</p>
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
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="openCancelModal({{ $req->id }}, '{{ route('my-services.destroy', $req) }}')">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px;">
            <a href="{{ route('my-services.index') }}" class="btn btn-secondary btn-sm">View all requests →</a>
        </div>
    @endif
</div>

{{-- History Tab --}}
<div data-panel="history" style="display:none;">
    <div class="page-header" style="margin-bottom:16px;">
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);">Request History</h2>
        <p style="font-size:.85rem;color:var(--muted);">Your completed and cancelled requests.</p>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('dashboard') }}" id="historyFilterForm"
        style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:18px;padding:16px 18px;background:var(--warm);border:1px solid var(--border);border-radius:10px;">

        {{-- Keep active tab on submit --}}
        <input type="hidden" name="tab" value="history">

        <div style="display:flex;flex-direction:column;gap:4px;min-width:140px;">
            <label style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);">Status</label>
            <select name="history_status" class="form-control" style="font-size:.87rem;padding:7px 10px;">
                <option value="">All</option>
                <option value="Completed" {{ ($historyStatus ?? '') === 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ ($historyStatus ?? '') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
            <label style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);">Service Type</label>
            <select name="history_service_type" class="form-control" style="font-size:.87rem;padding:7px 10px;">
                <option value="">All</option>
                @foreach($historyServiceTypes ?? [] as $type)
                    <option value="{{ $type }}" {{ ($historyServiceType ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex;flex-direction:column;gap:4px;min-width:130px;">
            <label style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);">Priority</label>
            <select name="history_priority" class="form-control" style="font-size:.87rem;padding:7px 10px;">
                <option value="">All</option>
                <option value="Urgent"    {{ ($historyPriority ?? '') === 'Urgent'    ? 'selected' : '' }}>🚨 Urgent</option>
                <option value="Scheduled" {{ ($historyPriority ?? '') === 'Scheduled' ? 'selected' : '' }}>🗓 Scheduled</option>
                <option value="Low"       {{ ($historyPriority ?? '') === 'Low'       ? 'selected' : '' }}>✅ Low</option>
            </select>
        </div>

        <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:1px;">
            <button type="submit" class="btn btn-sm" style="height:36px;">Filter</button>
            @if(($historyStatus ?? '') || ($historyServiceType ?? '') || ($historyPriority ?? ''))
                <a href="{{ route('dashboard') }}?tab=history" class="btn btn-secondary btn-sm" style="height:36px;line-height:36px;padding-top:0;padding-bottom:0;">Clear</a>
            @endif
        </div>
    </form>

    @if(isset($historyRequests) && $historyRequests->isEmpty())
        <div class="card">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                <h3>No history found</h3>
                <p>Try adjusting your filters, or completed/cancelled requests will appear here.</p>
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
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyRequests as $req)
                    <tr>
                        <td><strong>{{ $req->service_type }}</strong></td>
                        <td style="max-width:200px;color:var(--muted);font-size:.87rem;">{{ $req->details ? Str::limit($req->details,60) : '—' }}</td>
                        <td>
                            @php $p = strtolower($req->priority ?? 'normal'); @endphp
                            <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                        </td>
                        <td>
                            @php $s=$req->status??'Open';$cls=match($s){'Completed'=>'badge-done','Cancelled'=>'badge-cancelled',default=>'badge-normal'}; @endphp
                            <span class="badge {{ $cls }}">{{ $s }}</span>
                        </td>
                        <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">{{ $req->updated_at->format('M d, Y') }}</td>
                        <td><a href="{{ route('my-services.show', $req) }}" class="btn btn-secondary btn-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($historyRequests->hasPages())
            <div style="margin-top:14px;">
                {{ $historyRequests->appends(request()->except('history_page'))->links() }}
            </div>
        @endif
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- STAFF DASHBOARD                                              --}}
{{-- ════════════════════════════════════════════════════════════ --}}
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
    </div>
</div>

<div class="page-header" style="margin-bottom:16px;">
    <h2 style="font-family:'DM Serif Display',serif;font-size:1.3rem;color:var(--ink);">Pending & In-Progress Requests</h2>
    <p>Most recent open requests that need attention.</p>
</div>

{{-- Staff Tab Navigation --}}
<div style="display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:0;">
    <button onclick="showTab('active')" data-tab="active"
        style="padding:9px 20px;border:none;background:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;color:var(--gold);border-bottom:2px solid var(--gold);margin-bottom:-2px;">
        Active Requests
    </button>
    <button onclick="showTab('history')" data-tab="history"
        style="padding:9px 20px;border:none;background:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;color:var(--muted);border-bottom:2px solid transparent;margin-bottom:-2px;">
        History
    </button>
</div>

{{-- Active Requests Panel --}}
<div data-panel="active">
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
                        @php $s=$req->status??'Open';$cls=match($s){'Open'=>'badge-open','In Progress'=>'badge-progress',default=>'badge-normal'}; @endphp
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
</div>{{-- end panel-active --}}

{{-- History Panel --}}
<div data-panel="history" style="display:none;">
    <div class="page-header" style="margin-bottom:16px;">
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);">Completed & Cancelled Tasks</h2>
        <p style="font-size:.85rem;color:var(--muted);">All resolved service requests.</p>
    </div>
    @if(isset($historyRequests) && $historyRequests->isEmpty())
        <div class="card">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                <h3>No history yet</h3>
                <p>Completed or cancelled requests will appear here.</p>
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
                        <th>Resolved</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyRequests as $req)
                    <tr>
                        <td><strong>{{ $req->guest->full_name ?? '—' }}</strong><br><span style="color:var(--muted);font-size:.82rem;">Room {{ $req->guest->room_number ?? '—' }}</span></td>
                        <td>{{ $req->service_type }}</td>
                        <td>
                            @php $p=strtolower($req->priority??'normal'); @endphp
                            <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                        </td>
                        <td>
                            @php $cls = $req->status === 'Completed' ? 'badge-done' : 'badge-cancelled'; @endphp
                            <span class="badge {{ $cls }}">{{ $req->status }}</span>
                        </td>
                        <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">{{ $req->updated_at->format('M d, Y g:i A') }}</td>
                        <td><a href="{{ route('service-requests.show', $req) }}" class="btn btn-secondary btn-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>{{-- end panel-history --}}

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ADMIN DASHBOARD                                              --}}
{{-- ════════════════════════════════════════════════════════════ --}}
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
        <p>Read-only overview — monitor guest requests and staff activity.</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
        <a href="{{ route('admin.users.index') }}" class="btn">Manage Users</a>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">View Guests</a>
        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">View All Requests</a>
        <a href="{{ route('dashboard') }}#history" onclick="setTimeout(()=>showTab('history'),100)" class="btn btn-secondary">Full History</a>
    </div>
</div>

{{-- Admin Tab Navigation --}}
<div style="display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:0;">
    <button onclick="showTab('active')" data-tab="active"
        style="padding:9px 20px;border:none;background:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;color:var(--gold);border-bottom:2px solid var(--gold);margin-bottom:-2px;">
        Active Requests
    </button>
    <button onclick="showTab('history')" data-tab="history"
        style="padding:9px 20px;border:none;background:none;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;color:var(--muted);border-bottom:2px solid transparent;margin-bottom:-2px;">
        History
    </button>
</div>

{{-- Active Tab --}}
<div data-panel="active">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

        <div>
            <div class="page-header" style="margin-bottom:12px;">
                <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);">Recent Active Requests</h2>
                <p style="font-size:.82rem;color:var(--muted);">Open and in-progress requests with handler info.</p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Type</th>
                            <th>Handled By</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRequests as $req)
                        <tr>
                            <td style="font-size:.87rem;">{{ $req->guest->full_name ?? '—' }}</td>
                            <td style="font-size:.87rem;">{{ $req->service_type }}</td>
                            <td style="font-size:.87rem;">
                                @if($req->user)
                                    <span style="font-weight:600;">{{ $req->user->name }}</span>
                                    <br><span style="color:var(--muted);font-size:.78rem;">{{ ucfirst($req->user->role) }}</span>
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @php $s=$req->status??'Open';$cls=match($s){'Open'=>'badge-open','In Progress'=>'badge-progress',default=>'badge-normal'}; @endphp
                                <span class="badge {{ $cls }}" style="font-size:.72rem;">{{ $s }}</span>
                            </td>
                            <td><a href="{{ route('service-requests.show',$req) }}" class="btn btn-secondary btn-sm">View</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">No active requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:10px;">
                <a href="{{ route('service-requests.index') }}" class="btn btn-secondary btn-sm">View all requests →</a>
            </div>
        </div>

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
</div>

{{-- History Tab --}}
<div data-panel="history" style="display:none;">
    <div class="page-header" style="margin-bottom:16px;">
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);">Recent Completed & Cancelled</h2>
        <p style="font-size:.85rem;color:var(--muted);">Last 10 resolved requests.</p>
    </div>

    @if(isset($historyRequests) && $historyRequests->isEmpty())
        <div class="card">
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                <h3>No history yet</h3>
                <p>Completed or cancelled requests will appear here.</p>
            </div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Service Type</th>
                        <th>Handled By</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyRequests as $req)
                    <tr>
                        <td style="font-size:.87rem;font-weight:500;">{{ $req->guest->full_name ?? '—' }}</td>
                        <td style="font-size:.87rem;">{{ $req->service_type }}</td>
                        <td style="font-size:.87rem;">
                            @if($req->user)
                                <span style="font-weight:600;">{{ $req->user->name }}</span>
                            @else
                                <span style="color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @php $p=strtolower($req->priority??'normal'); @endphp
                            <span class="badge badge-{{ $p }}" style="font-size:.72rem;">{{ ucfirst($p) }}</span>
                        </td>
                        <td>
                            @php $cls = $req->status === 'Completed' ? 'badge-done' : 'badge-cancelled'; @endphp
                            <span class="badge {{ $cls }}" style="font-size:.72rem;">{{ $req->status }}</span>
                        </td>
                        <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">{{ $req->updated_at->format('M d, Y') }}</td>
                        <td><a href="{{ route('service-requests.show',$req) }}" class="btn btn-secondary btn-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endif

{{-- ── CANCEL REQUEST MODAL ── --}}
<div id="cancelModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:32px 28px;max-width:440px;width:90%;box-shadow:0 8px 40px rgba(0,0,0,.18);position:relative;">
        <h3 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0 0 6px;">Cancel Request</h3>
        <p style="font-size:.88rem;color:var(--muted);margin:0 0 20px;">Please let us know why you're cancelling. This helps our team improve.</p>
        <form id="cancelForm" method="POST">
            @csrf @method('DELETE')
            <div class="form-group" style="margin-bottom:18px;">
                <label class="form-label" for="cancellation_reason" style="font-size:.85rem;">Reason for Cancellation <span style="color:var(--rust)">*</span></label>
                <textarea id="cancellation_reason" name="cancellation_reason"
                    class="form-control"
                    rows="3"
                    placeholder="e.g. No longer needed, already handled by staff…"
                    style="resize:vertical;"></textarea>
                <div id="cancelError" style="display:none;color:var(--rust);font-size:.8rem;margin-top:4px;">Please provide a reason before cancelling.</div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">Go Back</button>
                <button type="button" class="btn btn-danger" onclick="submitCancel()">Confirm Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Tab switching script --}}
<script>
function openCancelModal(id, action) {
    document.getElementById('cancelForm').action = action;
    document.getElementById('cancellation_reason').value = '';
    document.getElementById('cancelError').style.display = 'none';
    const modal = document.getElementById('cancelModal');
    modal.style.display = 'flex';
    setTimeout(() => document.getElementById('cancellation_reason').focus(), 50);
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

function submitCancel() {
    const reason = document.getElementById('cancellation_reason').value.trim();
    if (!reason) {
        document.getElementById('cancelError').style.display = 'block';
        return;
    }
    document.getElementById('cancelForm').submit();
}

// Close modal when clicking the backdrop
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});

function showTab(tab) {
    // Find the tab buttons and panels that are actually visible in the DOM
    // (only one role section renders at a time, but IDs may repeat in source)
    const allPanelActive  = document.querySelectorAll('[data-panel="active"]');
    const allPanelHistory = document.querySelectorAll('[data-panel="history"]');
    const allTabActive    = document.querySelectorAll('[data-tab="active"]');
    const allTabHistory   = document.querySelectorAll('[data-tab="history"]');

    allPanelActive.forEach(el  => el.style.display = tab === 'active'  ? 'block' : 'none');
    allPanelHistory.forEach(el => el.style.display = tab === 'history' ? 'block' : 'none');

    allTabActive.forEach(btn => {
        btn.style.color        = tab === 'active' ? 'var(--gold)' : 'var(--muted)';
        btn.style.borderBottom = tab === 'active' ? '2px solid var(--gold)' : '2px solid transparent';
    });
    allTabHistory.forEach(btn => {
        btn.style.color        = tab === 'history' ? 'var(--gold)' : 'var(--muted)';
        btn.style.borderBottom = tab === 'history' ? '2px solid var(--gold)' : '2px solid transparent';
    });
}

// Auto-open the correct tab on page load (e.g. after filtering)
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab === 'history') {
        showTab('history');
    }
});
</script>

@endsection