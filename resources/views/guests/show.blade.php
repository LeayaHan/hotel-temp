@extends('layouts.app')

@section('content')
@if(Auth::user()->role !== 'staff' && Auth::user()->role !== 'admin')
    <div class="card"><p>Unauthorized.</p></div>
@else

<div class="card" style="max-width:600px;">
    <div class="page-header">
        <h2 style="margin:0;">Guest Details</h2>
        <div class="actions">
            <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm">Edit</a>
            <a href="{{ route('guests.index') }}" class="btn btn-secondary btn-sm">← Back</a>
        </div>
    </div>

    <div class="section-label">Personal Information</div>
    <table style="margin-top:0; border-radius:0; border:none; background:transparent;">
        <tbody>
            <tr>
                <td style="border:none; padding:8px 0; width:140px; font-weight:600; color:var(--espresso);">Full Name</td>
                <td style="border:none; padding:8px 0;">{{ $guest->full_name }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Email</td>
                <td style="border:none; padding:8px 0;">{{ $guest->email }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Phone</td>
                <td style="border:none; padding:8px 0;">{{ $guest->phone ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-label" style="margin-top:20px;">Stay Details</div>
    <table style="margin-top:0; border-radius:0; border:none; background:transparent;">
        <tbody>
            <tr>
                <td style="border:none; padding:8px 0; width:140px; font-weight:600; color:var(--espresso);">Room</td>
                <td style="border:none; padding:8px 0;">{{ $guest->room_number ?? '—' }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Check In</td>
                <td style="border:none; padding:8px 0;">{{ $guest->check_in ?? '—' }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Check Out</td>
                <td style="border:none; padding:8px 0;">{{ $guest->check_out ?? '—' }}</td>
            </tr>
            <tr>
                <td style="border:none; padding:8px 0; font-weight:600; color:var(--espresso);">Status</td>
                <td style="border:none; padding:8px 0;">
                    @php
                        $statusClass = match($guest->status) {
                            'Checked In'  => 'checked-in',
                            'Checked Out' => 'checked-out',
                            'Reserved'    => 'reserved',
                            default       => 'pending',
                        };
                    @endphp
                    <span class="status-badge status-{{ $statusClass }}">{{ $guest->status }}</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endif
@endsection