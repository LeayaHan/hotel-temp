@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>{{ $guest->full_name }}</h1>
    <p>Guest profile and stay details.</p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:800px;">

    <div class="card">
        <h2>Contact Info</h2>
        <table style="border:none;background:transparent;width:100%;">
            <tbody>
                <tr>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;width:110px;">Email</td>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);font-size:.9rem;">{{ $guest->email }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:var(--muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Phone</td>
                    <td style="padding:8px 0;font-size:.9rem;">{{ $guest->phone ?: '—' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Stay Details</h2>
        @php
            $cls = match($guest->status) {
                'Checked In'  => 'badge-done',
                'Checked Out' => 'badge-cancelled',
                'Reserved'    => 'badge-open',
                default       => 'badge-normal'
            };
        @endphp
        <table style="border:none;background:transparent;width:100%;">
            <tbody>
                <tr>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;width:110px;">Room</td>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);font-size:.9rem;">{{ $guest->room_number ?: '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Check-in</td>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);font-size:.9rem;">
                        {{ $guest->check_in ? \Carbon\Carbon::parse($guest->check_in)->format('M d, Y') : '—' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);color:var(--muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Check-out</td>
                    <td style="padding:8px 0;border-bottom:1px solid var(--border);font-size:.9rem;">
                        {{ $guest->check_out ? \Carbon\Carbon::parse($guest->check_out)->format('M d, Y') : '—' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:var(--muted);font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Status</td>
                    <td style="padding:8px 0;"><span class="badge {{ $cls }}">{{ $guest->status }}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="form-actions" style="margin-top:4px;">
    <a href="{{ route('guests.edit', $guest) }}" class="btn">Edit Guest</a>
    <form method="POST" action="{{ route('guests.destroy', $guest) }}"
          onsubmit="return confirm('Delete this guest? This cannot be undone.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Guest</button>
    </form>
    <a href="{{ route('guests.index') }}" class="btn btn-secondary">← Back to Guests</a>
</div>

@endsection