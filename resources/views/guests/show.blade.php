@extends('layouts.app')

@section('content')

@php $canManage = in_array(Auth::user()->role, ['admin', 'staff', 'front_desk']); @endphp

<div class="page-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>{{ $guest->full_name }}</h1>
            <p>Guest profile and stay details.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @if($canManage)
                <a href="{{ route('guests.edit', $guest) }}" class="btn">Edit Guest</a>
            @endif
            <a href="{{ route('guests.index') }}" class="btn btn-secondary">← Back to Guests</a>
        </div>
    </div>
</div>

{{-- ── GUEST DETAILS ── --}}
<div class="card" style="margin-bottom:24px;">
    <h2>Guest Details</h2>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 40px;">

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Full Name</div>
            <div style="font-size:.97rem;font-weight:600;">{{ $guest->full_name }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Status</div>
            @php
                $s = $guest->status ?? '';
                $cls = match($s) {
                    'Checked In'  => 'badge-done',
                    'Checked Out' => 'badge-cancelled',
                    'Reserved'    => 'badge-progress',
                    default       => 'badge-normal'
                };
            @endphp
            <span class="badge {{ $cls }}">{{ $s ?: '—' }}</span>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Email</div>
            <div style="font-size:.95rem;">{{ $guest->email }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Phone</div>
            <div style="font-size:.95rem;">{{ $guest->phone ?? '—' }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Floor Number</div>
            <div style="font-size:.97rem;font-weight:600;">{{ $guest->floor_number ?? '—' }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Room Number</div>
            <div style="font-size:.97rem;font-weight:600;">{{ $guest->room_number ?? '—' }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Registered</div>
            <div style="font-size:.95rem;color:var(--muted);">{{ $guest->created_at->format('M d, Y') }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Check In</div>
            <div style="font-size:.95rem;">
                {{ $guest->check_in ? \Carbon\Carbon::parse($guest->check_in)->format('M d, Y') : '—' }}
            </div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Check Out</div>
            <div style="font-size:.95rem;">
                {{ $guest->check_out ? \Carbon\Carbon::parse($guest->check_out)->format('M d, Y') : '—' }}
            </div>
        </div>

    </div>
</div>

{{-- ── SERVICE REQUESTS ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px;">
    <div>
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0;">Service Requests</h2>
        <p style="color:var(--muted);font-size:.85rem;margin-top:2px;">All requests submitted for this guest.</p>
    </div>
    @if($canManage)
        <a href="{{ route('service-requests.create') }}" class="btn btn-secondary btn-sm">+ New Request</a>
    @endif
</div>

@if($guest->serviceRequests && $guest->serviceRequests->count())
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Details</th>
                    <th>Handled By</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($guest->serviceRequests()->with('user')->latest()->get() as $req)
                <tr>
                    <td><strong>{{ $req->service_type }}</strong></td>
                    <td style="color:var(--muted);font-size:.87rem;max-width:200px;">
                        {{ $req->details ? \Illuminate\Support\Str::limit($req->details, 60) : '—' }}
                    </td>
                    <td style="font-size:.87rem;">
                        @if($req->user)
                            <span style="font-weight:600;">{{ $req->user->name }}</span>
                            <br><span style="color:var(--muted);font-size:.78rem;">{{ ucfirst($req->user->role) }}</span>
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @php $p = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php
                            $rs = $req->status ?? 'Open';
                            $rc = match($rs) {
                                'Open'        => 'badge-open',
                                'In Progress' => 'badge-progress',
                                'Completed'   => 'badge-done',
                                'Cancelled'   => 'badge-cancelled',
                                default       => 'badge-normal'
                            };
                        @endphp
                        <span class="badge {{ $rc }}">{{ $rs }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">
                        {{ $req->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('service-requests.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="card">
        <div class="empty-state" style="padding:32px 24px;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            <h3 style="font-size:1rem;">No requests yet</h3>
            <p>This guest has not submitted any service requests.</p>
        </div>
    </div>
@endif

{{-- ── DANGER ZONE (admin, staff, front_desk) ── --}}
@if($canManage)
<div class="card" style="margin-top:32px;border-color:#fca5a5;">
    <h2 style="color:var(--rust);">Danger Zone</h2>
    <p style="color:var(--muted);font-size:.9rem;margin-bottom:16px;">Deleting this guest is permanent and cannot be undone.</p>
    <form method="POST" action="{{ route('guests.destroy', $guest) }}" onsubmit="return confirm('Permanently delete {{ addslashes($guest->full_name) }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Guest</button>
    </form>
</div>
@endif

@endsection