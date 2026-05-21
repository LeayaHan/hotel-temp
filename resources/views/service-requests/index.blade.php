@extends('layouts.app')

@section('content')

<div class="page-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>Service Requests</h1>
            <p>Open and in-progress service requests.</p>
        </div>
    </div>
</div>

{{-- ── FILTERS ── --}}
<div class="card" style="margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('service-requests.index') }}">

        {{-- Row 1: Search + Guest + Service Type --}}
        <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;margin-bottom:.75rem;">

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

            <div class="form-group" style="margin:0;flex:1;min-width:180px;">
                <label class="form-label">Service Type</label>
                <select name="service_type" class="form-control">
                    <option value="">All Types</option>
                    @foreach([
                        'Room Cleaning',
                        'Extra Towels / Linens',
                        'Room Service / Food',
                        'Maintenance / Repair',
                        'Luggage Assistance',
                        'Wake-up Call',
                        'Laundry Service',
                        'Transportation / Taxi',
                        'Toiletries',
                        'Concierge / Information',
                        'Other',
                    ] as $type)
                        <option value="{{ $type }}" {{ request('service_type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Row 2: Priority + Status + Floor + Date Range + Buttons --}}
        <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">

            <div class="form-group" style="margin:0;min-width:130px;">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-control">
                    <option value="">All</option>
                    @foreach(['Low', 'Scheduled', 'Urgent'] as $p)
                        <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;min-width:130px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    @foreach(['Open', 'In Progress', 'Completed', 'Cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;min-width:110px;">
                <label class="form-label">Floor</label>
                <select name="floor" class="form-control">
                    <option value="">All Floors</option>
                    @foreach($floors as $floor)
                        <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>
                            Floor {{ $floor }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin:0;min-width:140px;">
                <label class="form-label">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>

            <div class="form-group" style="margin:0;min-width:140px;">
                <label class="form-label">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>

            <div style="display:flex;gap:.5rem;padding-bottom:1px;">
                <button type="submit" class="btn">Filter</button>
                <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>

    </form>
</div>

{{-- Active filter indicator --}}
@if(request()->hasAny(['guest_id', 'service_type', 'priority', 'status', 'floor', 'date_from', 'date_to']))
<p style="font-size:.83rem;color:var(--muted);margin:-1rem 0 1rem;">
    Showing filtered results —
    <a href="{{ route('service-requests.index') }}" style="color:var(--rust);">clear filters</a>
</p>
@endif

@if($serviceRequests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <h3>No active requests</h3>
            <p style="margin-bottom:20px;">You have no open or in-progress requests.</p>
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
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceRequests as $i => $req)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $serviceRequests->firstItem() + $i }}</td>
                    <td><strong>{{ $req->service_type }}</strong></td>
                    <td style="max-width:200px;color:var(--muted);font-size:.87rem;">
                        {{ $req->details ? Str::limit($req->details, 60) : '—' }}
                    </td>
                    <td>
                        @php $pri = strtolower($req->priority ?? 'normal'); @endphp
                        <span class="badge badge-{{ $pri }}">{{ ucfirst($pri) }}</span>
                    </td>
                    <td>
                        @php
                            $s = $req->status ?? 'Open';
                            $cls = match($s) {
                                'Open' => 'badge-open',
                                'In Progress' => 'badge-progress',
                                'Completed' => 'badge-done',
                                'Cancelled' => 'badge-cancelled',
                                default => 'badge-normal'
                            };
                        @endphp
                        <span class="badge {{ $cls }}">{{ $s }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">
                        {{ $req->created_at->format('M d, Y') }}
                    </td>
                    <td style="white-space:nowrap;">
                        <div style="display:flex;gap:6px;align-items:center;">
                            <a href="{{ route('service-requests.show', $req) }}" class="btn btn-secondary btn-sm">View</a>

                            @if(!in_array($req->status, ['Completed', 'Cancelled']))
                                <button type="button" class="btn btn-sm"
                                    style="background:var(--gold);color:#fff;border:none;"
                                    onclick="openStatusModal(
                                        '{{ route('service-requests.update', $req) }}',
                                        '{{ $req->status }}'
                                    )">
                                    Update Status
                                </button>

                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="openCancelModal('{{ route('service-requests.destroy', $req) }}')">
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

    <div class="pagination">
        {{ $serviceRequests->links() }}
    </div>
@endif

{{-- ── UPDATE STATUS MODAL ── --}}
<div id="statusModal" style="display:none;position:fixed;inset:0;z-index:999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:32px 28px;max-width:420px;width:90%;box-shadow:0 8px 40px rgba(0,0,0,.18);">
        <h3 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0 0 6px;">Update Status</h3>
        <p style="font-size:.88rem;color:var(--muted);margin:0 0 20px;">Select the new status for this service request.</p>

        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">New Status <span style="color:var(--rust)">*</span></label>
                <select id="statusSelect" name="status" class="form-control">
                    <option value="Open">Open</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeStatusModal()" class="btn btn-secondary">Back</button>
                <button type="submit" class="btn">Save Status</button>
            </div>
        </form>
    </div>
</div>

{{-- ── CANCEL MODAL ── --}}
<div id="cancelModal" style="display:none;position:fixed;inset:0;z-index:999;background:rgba(0,0,0,.45);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:32px 28px;max-width:440px;width:90%;box-shadow:0 8px 40px rgba(0,0,0,.18);">
        <h3 style="font-family:'DM Serif Display',serif;font-size:1.2rem;color:var(--ink);margin:0 0 6px;">Cancel Request</h3>
        <p style="font-size:.88rem;color:var(--muted);margin:0 0 20px;">Please let us know why you're cancelling this request.</p>

        <form id="cancelForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="form-group" style="margin-bottom:18px;">
                <label class="form-label" for="cancellation_reason">Reason <span style="color:var(--rust)">*</span></label>
                <textarea id="cancellation_reason" name="cancellation_reason" class="form-control"
                    rows="3" placeholder="e.g. No longer needed, already handled…"
                    style="resize:vertical;"></textarea>
                <div id="cancelReasonError" style="display:none;color:var(--rust);font-size:.8rem;margin-top:4px;">Please enter a reason.</div>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeCancelModal()" class="btn btn-secondary">Back</button>
                <button type="submit" class="btn btn-danger" onclick="return validateCancel()">Confirm Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatusModal(actionUrl, currentStatus) {
    document.getElementById('statusForm').action = actionUrl;
    document.getElementById('statusSelect').value = currentStatus;
    const modal = document.getElementById('statusModal');
    modal.style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});

function openCancelModal(actionUrl) {
    document.getElementById('cancelForm').action = actionUrl;
    document.getElementById('cancellation_reason').value = '';
    document.getElementById('cancelReasonError').style.display = 'none';
    const modal = document.getElementById('cancelModal');
    modal.style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

function validateCancel() {
    const reason = document.getElementById('cancellation_reason').value.trim();
    if (!reason) {
        document.getElementById('cancelReasonError').style.display = 'block';
        return false;
    }
    return true;
}

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>

@endsection