@extends('layouts.app')

@section('content')

@php $canManage = in_array(Auth::user()->role, ['admin', 'staff', 'front_desk']); @endphp

@if(session('temp_password'))
<div class="card" style="border-color:#86efac;background:#f0fdf4;margin-bottom:20px;">
    <h3 style="color:#166534;margin-bottom:8px;">✅ Guest account created</h3>
    <p style="color:#166534;font-size:.93rem;">Share these temporary credentials with the guest:</p>
    <p style="margin-top:8px;"><strong>Email:</strong> {{ session('temp_email') }}</p>
    <p><strong>Temporary Password:</strong>
        <code style="background:#dcfce7;padding:2px 8px;border-radius:4px;font-size:1rem;letter-spacing:.05em;">
            {{ session('temp_password') }}
        </code>
    </p>
    <p style="color:#6b7280;font-size:.82rem;margin-top:8px;">The guest should log in and change their password.</p>
</div>
@endif

<div class="page-header">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>Guests</h1>
            <p>Manage all registered guests and their room assignments.</p>
        </div>
        @if($canManage)
            <a href="{{ route('guests.create') }}" class="btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Guest
            </a>
        @endif
    </div>
</div>

@if($guests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <h3>No guests yet</h3>
            @if($canManage)
                <p style="margin-bottom:20px;">Add your first guest to get started.</p>
                <a href="{{ route('guests.create') }}" class="btn" style="display:inline-flex;">Add Guest</a>
            @endif
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Floor</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guests as $guest)
                <tr>
                    <td><strong>{{ $guest->full_name }}</strong></td>
                    <td style="color:var(--muted);font-size:.87rem;">{{ $guest->email }}</td>
                    <td style="color:var(--muted);font-size:.87rem;">{{ $guest->phone ?? '—' }}</td>
                    <td>
                        @if($guest->floor_number)
                            <span style="font-weight:600;">{{ $guest->floor_number }}</span>
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($guest->room_number)
                            <span style="font-weight:600;">{{ $guest->room_number }}</span>
                        @else
                            <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td style="font-size:.87rem;white-space:nowrap;">
                        {{ $guest->check_in ? \Carbon\Carbon::parse($guest->check_in)->format('M d, Y') : '—' }}
                    </td>
                    <td style="font-size:.87rem;white-space:nowrap;">
                        {{ $guest->check_out ? \Carbon\Carbon::parse($guest->check_out)->format('M d, Y') : '—' }}
                    </td>
                    <td>
                        @php
                            $s = $guest->status ?? '';
                            $cls = match($s) {
                                'Checked In'  => 'badge-done',
                                'Checked Out' => 'badge-cancelled',
                                'Reserved'    => 'badge-progress',
                                default       => 'badge-normal'
                            };
                        @endphp
                        <span class="badge {{ $cls }}">{{ $s }}</span>
                    </td>
                    <td style="white-space:nowrap;">
                        <div style="display:flex;gap:6px;align-items:center;">
                            <a href="{{ route('guests.show', $guest) }}" class="btn btn-secondary btn-sm">View</a>
                            @if($canManage)
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('guests.destroy', $guest) }}" onsubmit="return confirm('Delete this guest?')" style="display:inline;">
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

    <div class="pagination">
        {{ $guests->links() }}
    </div>
@endif

@endsection