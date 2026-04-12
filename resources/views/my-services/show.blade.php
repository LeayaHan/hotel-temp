@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Request Details</h1>
    <p>Viewing service request #{{ $serviceRequest->id }}</p>
</div>

<div class="card" style="max-width:620px;">
    @php
        $s = $serviceRequest->status ?? 'Open';
        $cls = match($s) {
            'Open' => 'badge-open', 'In Progress' => 'badge-progress',
            'Completed' => 'badge-done', 'Cancelled' => 'badge-cancelled',
            default => 'badge-normal'
        };
        $p = strtolower($serviceRequest->priority ?? 'normal');
    @endphp

    <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
        <span class="badge {{ $cls }}" style="font-size:.82rem;">{{ $s }}</span>
        <span class="badge badge-{{ $p }}" style="font-size:.82rem;">{{ ucfirst($p) }} Priority</span>
    </div>

    <table style="border:none;background:transparent;">
        <tbody>
            <tr>
                <td style="padding:10px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:.82rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;width:160px;">Service Type</td>
                <td style="padding:10px 0;border-bottom:1px solid var(--border);font-weight:600;">{{ $serviceRequest->service_type }}</td>
            </tr>
            <tr>
                <td style="padding:10px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:.82rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Details</td>
                <td style="padding:10px 0;border-bottom:1px solid var(--border);">{{ $serviceRequest->details ?: '—' }}</td>
            </tr>
            <tr>
                <td style="padding:10px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:.82rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Submitted</td>
                <td style="padding:10px 0;border-bottom:1px solid var(--border);">{{ $serviceRequest->created_at->format('F d, Y \a\t g:i A') }}</td>
            </tr>
            <tr>
                <td style="padding:10px 0;color:var(--muted);font-size:.82rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Last Updated</td>
                <td style="padding:10px 0;">{{ $serviceRequest->updated_at->format('F d, Y \a\t g:i A') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="form-actions" style="margin-top:24px;">
        @if($serviceRequest->status === 'Open')
            <a href="{{ route('my-services.edit', $serviceRequest) }}" class="btn">Edit Request</a>
            <form method="POST" action="{{ route('my-services.destroy', $serviceRequest) }}"
                  onsubmit="return confirm('Cancel this request?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Cancel Request</button>
            </form>
        @endif
        <a href="{{ route('my-services.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

@endsection