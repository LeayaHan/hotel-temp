@extends('layouts.app')

@section('content')

<div class="page-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>Service Requests</h1>
            <p>All guest service requests across the system.</p>
        </div>
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('service-requests.create') }}" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Request
            </a>
        @endif
    </div>
</div>

@if($serviceRequests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
            <h3>No requests yet</h3>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Service Type</th>
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
                    <td><strong>{{ $req->guest->full_name ?? '—' }}</strong></td>
                    <td style="color:var(--muted);">{{ $req->guest->room_number ?? '—' }}</td>
                    <td>{{ $req->service_type }}</td>
                    <td>
                        @php $p=strtolower($req->priority??'normal'); @endphp
                        <span class="badge badge-{{ $p }}">{{ ucfirst($p) }}</span>
                    </td>
                    <td>
                        @php $s=$req->status??'Open';$cls=match($s){'Open'=>'badge-open','In Progress'=>'badge-progress','Completed'=>'badge-done','Cancelled'=>'badge-cancelled',default=>'badge-normal'}; @endphp
                        <span class="badge {{ $cls }}">{{ $s }}</span>
                    </td>
                    <td style="color:var(--muted);font-size:.85rem;white-space:nowrap;">{{ $req->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('service-requests.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('service-requests.edit', $req) }}" class="btn btn-sm">Update</a>
                            @if(Auth::user()->role === 'admin')
                                <form method="POST" action="{{ route('service-requests.destroy', $req) }}"
                                      onsubmit="return confirm('Delete this request?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $serviceRequests->links() }}</div>
@endif

@endsection