@extends('layouts.app')

@section('content')
@if(Auth::user()->role !== 'staff' && Auth::user()->role !== 'admin')
    <div class="card"><p>Unauthorized.</p></div>
@else

<div class="card" style="max-width:600px;">
    <div class="page-header">
        <h2 style="margin:0;">Request #{{ $serviceRequest->id }}</h2>
        <div class="actions">
            <a href="{{ route('service-requests.edit', $serviceRequest) }}" class="btn btn-sm">Edit</a>
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary btn-sm">← Back</a>
        </div>
    </div>

    <div class="section-label">Guest Information</div>
    <table style="margin-top:0; border:none; background:transparent;">
        <tbody>
            <tr>
                <td style="border:none; padding:8px 0; width:140px; font-weight:600; color:var(--espresso);">Name</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->guest->full_name ?? '—' }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Room</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->guest->room_number ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-label" style="margin-top:20px;">Request Details</div>
    <table style="margin-top:0; border:none; background:transparent;">
        <tbody>
            <tr>
                <td style="border:none; padding:8px 0; width:140px; font-weight:600; color:var(--espresso);">Service Type</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->service_type }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Details</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->details ?? '—' }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Priority</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->priority ?? 'Normal' }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Status</td>
                <td style="border:none; padding:8px 0;">
                    @php
                        $sc = match($serviceRequest->status) {
                            'Completed'   => 'completed',
                            'In Progress' => 'in-progress',
                            'Cancelled'   => 'cancelled',
                            default       => 'open',
                        };
                    @endphp
                    <span class="status-badge status-{{ $sc }}">{{ $serviceRequest->status }}</span>
                </td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Submitted</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->created_at->format('M d, Y h:i A') }}</td>
            </tr>
        </tbody>
    </table>
</div>

@endif
@endsections