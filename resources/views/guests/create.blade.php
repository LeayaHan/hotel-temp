@extends('layouts.app')

@section('content')
{{-- Staff / Admin only --}}
@if(Auth::user()->role !== 'staff' && Auth::user()->role !== 'admin')
    <div class="card">
        <p>You do not have permission to view this page.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="margin-top:12px;">Back to Dashboard</a>
    </div>
@else

<div class="card">
    <div class="page-header">
        <h2 style="margin:0;">Guest List</h2>
        <a href="{{ route('guests.create') }}" class="btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Guest
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th width="210">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                    <tr>
                        <td><strong>{{ $guest->full_name }}</strong></td>
                        <td>{{ $guest->email }}</td>
                        <td>{{ $guest->phone }}</td>
                        <td>{{ $guest->room_number }}</td>
                        <td>{{ $guest->check_in }}</td>
                        <td>{{ $guest->check_out }}</td>
                        <td>
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
                        <td>
                            <div class="actions">
                                <a href="{{ route('guests.show', $guest) }}" class="btn btn-secondary btn-sm">View</a>
                                <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm">Edit</a>
                                <form action="{{ route('guests.destroy', $guest) }}" method="POST" onsubmit="return confirm('Delete this guest?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <p>No guests found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $guests->links() }}
    </div>
</div>

@endif
@endsection