@extends('layouts.app')

@section('content')

<div class="page-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1>Guests</h1>
            <p>Manage all registered hotel guests.</p>
        </div>
        <a href="{{ route('guests.create') }}" class="btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Guest
        </a>
    </div>
</div>

@if($guests->isEmpty())
    <div class="card">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <h3>No guests yet</h3>
            <p style="margin-bottom:20px;">Add your first guest to get started.</p>
            <a href="{{ route('guests.create') }}" class="btn" style="display:inline-flex;">Add Guest</a>
        </div>
    </div>
@else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guests as $i => $guest)
                <tr>
                    <td style="color:var(--muted);font-size:.82rem;">{{ $guests->firstItem() + $i }}</td>
                    <td><strong>{{ $guest->full_name }}</strong></td>
                    <td style="color:var(--muted);font-size:.87rem;">{{ $guest->email }}</td>
                    <td style="color:var(--muted);font-size:.87rem;">{{ $guest->phone ?: '—' }}</td>
                    <td>{{ $guest->room_number ?: '—' }}</td>
                    <td style="font-size:.87rem;white-space:nowrap;">
                        {{ $guest->check_in ? \Carbon\Carbon::parse($guest->check_in)->format('M d, Y') : '—' }}
                    </td>
                    <td style="font-size:.87rem;white-space:nowrap;">
                        {{ $guest->check_out ? \Carbon\Carbon::parse($guest->check_out)->format('M d, Y') : '—' }}
                    </td>
                    <td>
                        @php
                            $cls = match($guest->status) {
                                'Checked In'  => 'badge-done',
                                'Checked Out' => 'badge-cancelled',
                                'Reserved'    => 'badge-open',
                                default       => 'badge-normal'
                            };
                        @endphp
                        <span class="badge {{ $cls }}">{{ $guest->status }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('guests.show', $guest) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('guests.edit', $guest) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('guests.destroy', $guest) }}"
                                  onsubmit="return confirm('Delete this guest?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $guests->links() }}</div>
@endif

@endsection