@extends('layouts.app')

@section('content')

<div class="page-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>Request History</h1>
            <p>All completed and cancelled service requests.</p>
        </div>
        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
            ← Back to Active Requests
        </a>
    </div>
</div>

{{-- ── FILTERS ── --}}
<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('service-requests.history') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">

        <div class="form-group" style="margin:0;flex:1;min-width:180px;">
            <label class="form-label">Guest</label>
            <select name="guest_id" class="form-control">
                <option value="">All Guests</option>
                @foreach($guests as $guest)
                    <option value="{{ $guest->id }}" {{ request('guest_id') == $guest->id ? 'selected' : '' }}>
                        {{ $guest->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin:0;min-width:160px;">
            <label class="form-label">Service Type</label>
            <select name="service_type" class="form-control">
                <option value="">All Types</option>
                @foreach($serviceTypes as $type)
                    <option value="{{ $type }}" {{ request('service_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin:0;min-width:130px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div class="form-group" style="margin:0;min-width:130px;">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-control">
                <option value="">All</option>
                @foreach(['Urgent', 'Scheduled', 'Low'] as $p)
                    <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin:0;min-width:110px;">
            <label class="form-label">Floor</label>
            <select name="floor" class="form-control">
                <option value="">All Floors</option>
                @foreach($floors as $floor)
                    <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>Floor {{ $floor }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin:0;min-width:110px;">
            <label class="form-label">Room</label>
            <input type="text" name="room" value="{{ request('room') }}"
                   placeholder="e.g. 107"
                   class="form-control">
        </div>

        <div class="form-group" style="margin:0;min-width:140px;">
            <label class="form-label">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>

        <div class="form-group" style="margin:0;min-width:140px;">
            <label class="form-label">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>

        <div style="display:flex;gap:.5rem;padding-bottom:1px;">
            <button type="submit" class="btn">Filter</button>
            <a href="{{ route('service-requests.history') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- Active filter indicator --}}
@if(request()->hasAny(['guest_id','priority','status','service_type','floor','room','date_from','date_to']))
<p style="font-size:.83rem;color:var(--muted);margin:-1rem 0 1rem;">
    Showing filtered results —
    <a href="{{ route('service-requests.history') }}" style="color:var(--rust);">clear filters</a>
</p>
@endif

@if($serviceRequests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
            </svg>
            <h3>No history yet</h3>
            <p>Completed and cancelled requests will appear here.</p>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Guest</th>
                    <th>Service Type</th>
                    <th>Floor / Room</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceRequests as $i => $req)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $serviceRequests->firstItem() + $i }}</td>
                    <td>
                        @if($req->guest)
                            <strong>{{ $req->guest->full_name }}</strong>
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td><strong>{{ $req->service_type }}</strong></td>
                    <td style="font-size:.87rem;color:var(--muted);">
                        {{ $req->floor ? 'Floor '.$req->floor : '—' }}
                        {{ $req->room_number ? ' / Room '.$req->room_number : '' }}
                    </td>
                    <td>
                        @php $p = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php
                            $cls = match($req->status) {
                                'Completed' => 'badge-done',
                                'Cancelled' => 'badge-cancelled',
                                default     => 'badge-normal'
                            };
                        @endphp
                        <span class="badge {{ $cls }}">{{ $req->status }}</span>
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

    <div class="pagination">
        {{ $serviceRequests->links() }}
    </div>
@endif

@endsection