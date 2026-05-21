@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>New Service Request</h1>
    <p>Create a service request on behalf of a guest.</p>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('service-requests.store') }}">
        @csrf

        {{-- ── GUEST ── --}}
        <div class="form-group">
            <label class="form-label" for="guest_id">
                Guest <span style="color:var(--rust)">*</span>
            </label>
            <select id="guest_id" name="guest_id" class="form-control" required
                onchange="fillRoomFloor(this)">
                <option value="">— Select a guest —</option>
                @foreach($guests as $guest)
                    <option value="{{ $guest->id }}"
                        data-room="{{ $guest->room_number }}"
                        {{ old('guest_id') == $guest->id ? 'selected' : '' }}>
                        {{ $guest->full_name }}
                        @if($guest->room_number) — Room {{ $guest->room_number }} @endif
                    </option>
                @endforeach
            </select>
            @error('guest_id')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── ROOM + FLOOR ── --}}
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label" for="room_number">Room Number</label>
                <input type="text" id="room_number" name="room_number"
                    class="form-control" placeholder="e.g. 205"
                    value="{{ old('room_number') }}">
                @error('room_number')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="floor">Floor</label>
                <input type="text" id="floor" name="floor"
                    class="form-control" placeholder="e.g. 3"
                    value="{{ old('floor') }}">
                @error('floor')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── SERVICE TYPE ── --}}
        <div class="form-group">
            <label class="form-label" for="service_type">
                Service Type <span style="color:var(--rust)">*</span>
            </label>
            <select id="service_type" name="service_type" class="form-control" required>
                <option value="">— Select service type —</option>
                @foreach([
                    'Room Cleaning',
                    'Extra Towels / Linens',
                    'Room Service / Food',
                    'Maintenance / Repair',
                    'Luggage Assistance',
                    'Wake-up Call',
                    'Laundry Service',
                    'Transportation / Taxi',
                    'Toiletries',
                    'Concierge / Information',
                    'Other',
                ] as $type)
                    <option value="{{ $type }}" {{ old('service_type') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('service_type')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- ── DETAILS ── --}}
        <div class="form-group">
            <label class="form-label" for="details">Details</label>
            <textarea id="details" name="details" class="form-control" rows="3"
                placeholder="Any additional notes or instructions...">{{ old('details') }}</textarea>
        </div>

        {{-- ── ITEMS / QUANTITIES ── --}}
        <div class="form-group">
            <label class="form-label" for="quantities">Items / Quantities</label>
            <textarea id="quantities" name="quantities" class="form-control" rows="2"
                placeholder='e.g. {"Towels": 2, "Soap": 3} or just a plain description'>{{ old('quantities') }}</textarea>
            <p style="font-size:.8rem;color:var(--muted);margin-top:4px;">
                List items and quantities if applicable.
            </p>
        </div>

        {{-- ── PRIORITY + STATUS ── --}}
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label" for="priority">Priority</label>
                <select id="priority" name="priority" class="form-control">
                    @foreach(['Low', 'Scheduled', 'Urgent'] as $p)
                        <option value="{{ $p }}" {{ old('priority', 'Low') == $p ? 'selected' : '' }}>
                            {{ $p }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    @foreach(['Open', 'In Progress', 'Completed', 'Cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', 'Open') == $s ? 'selected' : '' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ── SCHEDULED AT ── --}}
        <div class="form-group">
            <label class="form-label" for="scheduled_at">Scheduled At</label>
            <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                class="form-control" value="{{ old('scheduled_at') }}">
        </div>

        {{-- ── ACTIONS ── --}}
        <div class="form-actions">
            <button type="submit" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Create Request
            </button>
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">← Cancel</a>
        </div>
    </form>
</div>

{{-- Auto-fill room number from selected guest --}}
<script>
function fillRoomFloor(select) {
    const option = select.options[select.selectedIndex];
    const room = option.getAttribute('data-room');
    const roomInput = document.getElementById('room_number');
    if (room && !roomInput.value) {
        roomInput.value = room;
    }
}
// Run on page load in case old() repopulates the select
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('guest_id');
    if (select && select.value) fillRoomFloor(select);
});
</script>

@endsection