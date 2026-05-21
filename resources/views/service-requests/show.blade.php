@extends('layouts.app')

@section('content')

@php $isAdmin = Auth::user()->role === 'admin'; @endphp

<div class="card" style="max-width:600px;">
    <div class="page-header">
        <h2 style="margin:0;">Request #{{ $serviceRequest->id }}</h2>
        <div class="actions">
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary btn-sm">← Back</a>
        </div>
    </div>

    {{-- ── GUEST INFORMATION ── --}}
    <div class="section-label">Guest Information</div>
    <table style="margin-top:0; border:none; background:transparent;">
        <tbody>
            <tr>
                <td style="border:none; padding:8px 0; width:140px; font-weight:600; color:var(--espresso);">Name</td>
                <td style="border:none; padding:8px 0;">
                    {{ $serviceRequest->guest->full_name ?? '—' }}
                </td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Room</td>
                <td style="border:none; padding:8px 0;">
                    {{-- check request's own room_number first, fall back to guest's --}}
                    {{ $serviceRequest->room_number ?? $serviceRequest->guest->room_number ?? '—' }}
                </td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Floor</td>
                <td style="border:none; padding:8px 0;">
                    {{ $serviceRequest->floor ?? '—' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ── REQUEST DETAILS ── --}}
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

            {{-- Items / Quantities --}}
            @if(!empty($serviceRequest->quantities))
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso); vertical-align:top;">Items</td>
                <td style="border:none; padding:8px 0;">
                    @php
                        $items = is_array($serviceRequest->quantities)
                            ? $serviceRequest->quantities
                            : json_decode($serviceRequest->quantities, true);
                    @endphp
                    @if(is_array($items) && count($items))
                        <ul style="margin:0; padding-left:18px;">
                            @foreach($items as $item => $qty)
                                <li>{{ $item }}: <strong>{{ $qty }}</strong></li>
                            @endforeach
                        </ul>
                    @else
                        {{ $serviceRequest->quantities }}
                    @endif
                </td>
            </tr>
            @endif

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
            @if($serviceRequest->status === 'Cancelled' && $serviceRequest->cancellation_reason)
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso); vertical-align:top;">Cancellation Reason</td>
                <td style="border:none; padding:8px 0;">
                    <div style="background:#fff5f5;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;color:#991b1b;font-size:.9rem;line-height:1.6;">
                        {{ $serviceRequest->cancellation_reason }}
                    </div>
                </td>
            </tr>
            @endif
            @if($serviceRequest->scheduled_at)
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Scheduled</td>
                <td style="border:none; padding:8px 0;">{{ \Carbon\Carbon::parse($serviceRequest->scheduled_at)->format('M d, Y g:i A') }}</td>
            </tr>
            @endif
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Submitted</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->created_at->format('M d, Y h:i A') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ── HANDLED BY — only show once someone has taken the request ── --}}
    @if($serviceRequest->user && $serviceRequest->status !== 'Open')
    <div class="section-label" style="margin-top:20px;">Handled By</div>
    <table style="margin-top:0; border:none; background:transparent;">
        <tbody>
            <tr>
                <td style="border:none; padding:8px 0; width:140px; font-weight:600; color:var(--espresso);">Employee</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->user->name }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Role</td>
                <td style="border:none; padding:8px 0;">{{ ucfirst($serviceRequest->user->role) }}</td>
            </tr>
            @if($serviceRequest->user->department)
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Department</td>
                <td style="border:none; padding:8px 0;">{{ $serviceRequest->user->department }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    @elseif($serviceRequest->status === 'Open')
    <div class="section-label" style="margin-top:20px;">Handled By</div>
    <p style="color:var(--muted);font-size:.9rem;font-style:italic;margin-top:8px;">
        Not yet assigned — awaiting staff acceptance.
    </p>
    @endif
</div>

@endsection