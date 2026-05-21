@extends('layouts.app')

@section('content')

@php
    $roleLabels = [
        'admin'      => ['🔑 Administrator', 'var(--rust)'],
        'manager'    => ['📋 Manager', 'var(--gold)'],
        'staff'      => ['🧹 Housekeeping', 'var(--ink)'],
        'front_desk' => ['🖥 Front Desk', 'var(--ink)'],
        'customer'   => ['🛎 Guest', 'var(--muted)'],
    ];
    [$roleLabel, $roleColor] = $roleLabels[$user->role] ?? [$user->role, 'var(--ink)'];

    $serviceRequests = $user->guest?->serviceRequests ?? collect();
    $currentRequests = $serviceRequests->whereIn('status', ['Open', 'In Progress']);
    $pastRequests    = $serviceRequests->whereIn('status', ['Completed', 'Cancelled']);
@endphp

{{-- ── PAGE HEADER ── --}}
<div class="page-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>{{ $user->name }}</h1>
            <p style="color:{{ $roleColor }};">{{ $roleLabel }}</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn">Edit Account</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← Back to Users</a>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif

{{-- ── ACCOUNT INFORMATION ── --}}
<div class="card" style="margin-bottom:24px;">
    <h2 style="margin-bottom:20px;">Account Information</h2>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 40px;">

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Full Name</div>
            <div style="font-size:.97rem;font-weight:600;">{{ $user->name }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Account Status</div>
            @if($user->is_active)
                <span style="background:#d1fae5;color:#065f46;padding:.2rem .7rem;border-radius:999px;font-size:.82rem;font-weight:600;">Active</span>
            @else
                <span style="background:#fee2e2;color:#991b1b;padding:.2rem .7rem;border-radius:999px;font-size:.82rem;font-weight:600;">Inactive</span>
            @endif
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Email Address</div>
            <div style="font-size:.95rem;">{{ $user->email }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Role</div>
            <div style="font-size:.95rem;color:{{ $roleColor }};">{{ $roleLabel }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Department</div>
            <div style="font-size:.95rem;">{{ $user->department ?? '—' }}</div>
        </div>

        <div style="margin-bottom:20px;">
            <div class="form-label" style="margin-bottom:4px;">Member Since</div>
            <div style="font-size:.95rem;color:var(--muted);">{{ $user->created_at->format('M d, Y') }}</div>
        </div>

    </div>

    {{-- Password Reset Section --}}
    <div style="border-top:1px solid var(--border);padding-top:20px;margin-top:8px;">
        <div class="form-label" style="margin-bottom:4px;">Password</div>
        <p style="font-size:.88rem;color:var(--muted);margin-bottom:12px;">
            Passwords are encrypted and cannot be viewed. You can reset this account's password below.
        </p>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" id="password-reset-form">
            @csrf
            @method('PUT')
            {{-- Hidden fields to carry over existing values --}}
            <input type="hidden" name="name"       value="{{ $user->name }}">
            <input type="hidden" name="email"      value="{{ $user->email }}">
            <input type="hidden" name="role"       value="{{ $user->role }}">
            <input type="hidden" name="department" value="{{ $user->department }}">
            <input type="hidden" name="is_active"  value="{{ $user->is_active ? '1' : '0' }}">

            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
                <div class="form-group" style="margin:0;min-width:200px;">
                    <label class="form-label" style="font-size:.8rem;">New Password</label>
                    <input type="password" name="password"
                           placeholder="Min. 8 characters"
                           class="form-control @error('password') is-invalid @enderror"
                           style="font-size:.88rem;">
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group" style="margin:0;min-width:200px;">
                    <label class="form-label" style="font-size:.8rem;">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           placeholder="Re-enter password"
                           class="form-control"
                           style="font-size:.88rem;">
                </div>
                <button type="submit" class="btn btn-secondary"
                        style="padding:.45rem 1rem;font-size:.85rem;white-space:nowrap;">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── LINKED GUEST PROFILE ── --}}
@if($user->guest)
<div class="card" style="margin-bottom:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
        <h2 style="margin:0;">Linked Guest Profile</h2>
        <a href="{{ route('guests.show', $user->guest) }}" class="btn btn-secondary btn-sm">View Full Profile</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 40px;">
        <div style="margin-bottom:16px;">
            <div class="form-label" style="margin-bottom:4px;">Full Name</div>
            <div style="font-size:.95rem;font-weight:600;">{{ $user->guest->full_name }}</div>
        </div>

        <div style="margin-bottom:16px;">
            <div class="form-label" style="margin-bottom:4px;">Guest Status</div>
            @php
                $gs = $user->guest->status ?? '';
                $gc = match($gs) {
                    'Checked In'  => 'badge-done',
                    'Checked Out' => 'badge-cancelled',
                    'Reserved'    => 'badge-progress',
                    default       => 'badge-normal'
                };
            @endphp
            <span class="badge {{ $gc }}">{{ $gs ?: '—' }}</span>
        </div>

        <div style="margin-bottom:16px;">
            <div class="form-label" style="margin-bottom:4px;">Floor / Room</div>
            <div style="font-size:.95rem;">
                Floor {{ $user->guest->floor_number ?? '—' }} &nbsp;·&nbsp; Room {{ $user->guest->room_number ?? '—' }}
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <div class="form-label" style="margin-bottom:4px;">Check In / Check Out</div>
            <div style="font-size:.95rem;">
                {{ $user->guest->check_in ? \Carbon\Carbon::parse($user->guest->check_in)->format('M d, Y') : '—' }}
                &nbsp;→&nbsp;
                {{ $user->guest->check_out ? \Carbon\Carbon::parse($user->guest->check_out)->format('M d, Y') : '—' }}
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── CURRENT SERVICE REQUESTS ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px;">
    <div>
        <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0;">
            Current Requests
            @if($currentRequests->count())
                <span style="background:var(--gold);color:#fff;font-size:.7rem;padding:.1rem .5rem;border-radius:999px;vertical-align:middle;margin-left:6px;">
                    {{ $currentRequests->count() }}
                </span>
            @endif
        </h2>
        <p style="color:var(--muted);font-size:.85rem;margin-top:2px;">Open and in-progress requests.</p>
    </div>
</div>

@if($currentRequests->count())
    <div class="table-wrap" style="margin-bottom:28px;">
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Details</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($currentRequests->sortByDesc('created_at') as $req)
                <tr>
                    <td><strong>{{ $req->service_type }}</strong></td>
                    <td style="color:var(--muted);font-size:.87rem;max-width:220px;">
                        {{ $req->details ? \Illuminate\Support\Str::limit($req->details, 60) : '—' }}
                    </td>
                    <td>
                        @php $p = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php
                            $rc = match($req->status) {
                                'Open'        => 'badge-open',
                                'In Progress' => 'badge-progress',
                                default       => 'badge-normal'
                            };
                        @endphp
                        <span class="badge {{ $rc }}">{{ $req->status }}</span>
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
    <div class="card" style="margin-bottom:28px;">
        <div class="empty-state" style="padding:24px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            <h3 style="font-size:.95rem;">No active requests</h3>
            <p style="font-size:.85rem;">This user has no open or in-progress requests.</p>
        </div>
    </div>
@endif

{{-- ── PAST SERVICE REQUESTS ── --}}
<div style="margin-bottom:14px;">
    <h2 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0;">Past Requests</h2>
    <p style="color:var(--muted);font-size:.85rem;margin-top:2px;">Completed and cancelled requests.</p>
</div>

@if($pastRequests->count())
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Details</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pastRequests->sortByDesc('created_at') as $req)
                <tr>
                    <td><strong>{{ $req->service_type }}</strong></td>
                    <td style="color:var(--muted);font-size:.87rem;max-width:220px;">
                        {{ $req->details ? \Illuminate\Support\Str::limit($req->details, 60) : '—' }}
                    </td>
                    <td>
                        @php $p = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php
                            $rc = match($req->status) {
                                'Completed' => 'badge-done',
                                'Cancelled' => 'badge-cancelled',
                                default     => 'badge-normal'
                            };
                        @endphp
                        <span class="badge {{ $rc }}">{{ $req->status }}</span>
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
        <div class="empty-state" style="padding:24px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            <h3 style="font-size:.95rem;">No past requests</h3>
            <p style="font-size:.85rem;">No completed or cancelled requests on record.</p>
        </div>
    </div>
@endif

{{-- ── DANGER ZONE ── --}}
@if($user->id !== auth()->id())
<div class="card" style="margin-top:32px;border-color:#fca5a5;">
    <h2 style="color:var(--rust);">Danger Zone</h2>
    <p style="color:var(--muted);font-size:.9rem;margin-bottom:16px;">Deleting this account is permanent and cannot be undone.</p>
    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
          onsubmit="return confirm('Permanently delete account for {{ addslashes($user->name) }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Account</button>
    </form>
</div>
@endif

@endsection