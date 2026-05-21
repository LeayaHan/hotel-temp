@extends('layouts.app')

@section('content')

<div class="page-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>Request History</h1>
            <p>Your completed and cancelled service requests.</p>
        </div>
        <a href="{{ route('my-services.create') }}" class="btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Request
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('my-services.history') }}"
    style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px;padding:16px 18px;background:var(--warm);border:1px solid var(--border);border-radius:10px;">

    <div style="display:flex;flex-direction:column;gap:4px;min-width:140px;">
        <label style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);">Status</label>
        <select name="history_status" class="form-control" style="font-size:.87rem;padding:7px 10px;">
            <option value="">All</option>
            <option value="Completed" {{ request('history_status') === 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="Cancelled" {{ request('history_status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
        <label style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);">Service Type</label>
        <select name="history_service_type" class="form-control" style="font-size:.87rem;padding:7px 10px;">
            <option value="">All</option>
            @foreach($historyServiceTypes as $type)
                <option value="{{ $type }}" {{ request('history_service_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px;min-width:130px;">
        <label style="font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);">Priority</label>
        <select name="history_priority" class="form-control" style="font-size:.87rem;padding:7px 10px;">
            <option value="">All</option>
            <option value="Urgent"    {{ request('history_priority') === 'Urgent'    ? 'selected' : '' }}>🚨 Urgent</option>
            <option value="Scheduled" {{ request('history_priority') === 'Scheduled' ? 'selected' : '' }}>🗓 Scheduled</option>
            <option value="Low"       {{ request('history_priority') === 'Low'       ? 'selected' : '' }}>✅ Low</option>
        </select>
    </div>

    <div style="display:flex;gap:8px;align-items:flex-end;padding-bottom:1px;">
        <button type="submit" class="btn btn-sm" style="height:36px;">Filter</button>
        @if(request('history_status') || request('history_service_type') || request('history_priority'))
            <a href="{{ route('my-services.history') }}" class="btn btn-secondary btn-sm" style="height:36px;line-height:36px;padding-top:0;padding-bottom:0;">Clear</a>
        @endif
    </div>
</form>

@if($historyRequests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            <h3>No history found</h3>
            <p>Try adjusting your filters, or completed and cancelled requests will appear here.</p>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Service Type</th>
                    <th>Details</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($historyRequests as $i => $req)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $historyRequests->firstItem() + $i }}</td>
                    <td><strong>{{ $req->service_type }}</strong></td>
                    <td style="max-width:200px;color:var(--muted);font-size:.87rem;">
                        {{ $req->details ? Str::limit($req->details, 60) : '—' }}
                    </td>
                    <td>
                        @php $p = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php $cls = $req->status === 'Completed' ? 'badge-done' : 'badge-cancelled'; @endphp
                        <span class="badge {{ $cls }}">{{ $req->status }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">
                        {{ $req->updated_at->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('my-services.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($historyRequests->hasPages())
        <div style="margin-top:14px;">
            {{ $historyRequests->appends(request()->except('page'))->links() }}
        </div>
    @endif
@endif

@endsection