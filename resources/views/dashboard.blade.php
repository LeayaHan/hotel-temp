@extends('layouts.app')

@section('content')

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

{{-- Recent Requests Table --}}
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
                    <td style="max-width:200px;color:var(--muted);font-size:.87rem;">
                        {{ $req->details ? Str::limit($req->details, 60) : '—' }}
                    </td>
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
                        {{ $req->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('my-services.show', $req) }}" class="btn btn-secondary btn-sm">View</a>
                            @if($req->status === 'Open')
                                <a href="{{ route('my-services.edit', $req) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('my-services.destroy', $req) }}"
                                      onsubmit="return confirm('Cancel this request?')">
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

@endsection