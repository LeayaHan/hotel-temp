@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Edit Guest</h1>
    <p>Update details for <strong>{{ $guest->full_name }}</strong>.</p>
</div>

<div class="card">
    <form method="POST" action="{{ route('guests.update', $guest) }}">
        @csrf
        @method('PUT')

        {{-- Name Fields --}}
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">First Name <span style="color:var(--rust)">*</span></label>
                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                    value="{{ old('first_name', $guest->first_name) }}" placeholder="e.g. Juan">
                @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Last Name <span style="color:var(--rust)">*</span></label>
                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                    value="{{ old('last_name', $guest->last_name) }}" placeholder="e.g. dela Cruz">
                @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-group" style="max-width:200px;">
            <label class="form-label">Middle Initial <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
            <input type="text" name="middle_initial" class="form-control @error('middle_initial') is-invalid @enderror"
                value="{{ old('middle_initial', $guest->middle_initial) }}" placeholder="e.g. B" maxlength="5">
            @error('middle_initial')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $guest->email) }}" placeholder="e.g. juan@email.com">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $guest->phone) }}" placeholder="e.g. +63 912 345 6789">
                @error('phone')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Floor Number</label>
                <select id="floor_number" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror">
                    <option value="">— Select floor —</option>
                    @foreach(range(1, 6) as $f)
                        <option value="{{ $f }}" {{ old('floor_number', $guest->floor_number) == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                    @endforeach
                </select>
                @error('floor_number')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Room Number</label>
                <select id="room_number" name="room_number" class="form-control @error('room_number') is-invalid @enderror">
                    <option value="">— Select floor first —</option>
                </select>
                @error('room_number')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <script>
        (function () {
            const floorSel = document.getElementById('floor_number');
            const roomSel  = document.getElementById('room_number');
            const oldRoom  = '{{ old('room_number', $guest->room_number) }}';

            function populateRooms(floor, selected) {
                roomSel.innerHTML = '<option value="">— Select room —</option>';
                if (!floor) { roomSel.innerHTML = '<option value="">— Select floor first —</option>'; return; }
                for (let r = 1; r <= 10; r++) {
                    const roomNum = parseInt(floor) * 100 + r;
                    const opt = document.createElement('option');
                    opt.value = roomNum;
                    opt.textContent = 'Room ' + roomNum;
                    if (String(roomNum) === String(selected)) opt.selected = true;
                    roomSel.appendChild(opt);
                }
            }

            floorSel.addEventListener('change', () => populateRooms(floorSel.value, ''));
            // On page load populate rooms and restore saved value
            if (floorSel.value) populateRooms(floorSel.value, oldRoom);
        })();
        </script>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Status <span style="color:var(--rust)">*</span></label>
                <select name="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="">— Select status —</option>
                    @foreach(['Reserved', 'Checked In', 'Checked Out'] as $option)
                        <option value="{{ $option }}" {{ old('status', $guest->status) === $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                @error('status')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Check In</label>
                <input type="date" name="check_in" class="form-control @error('check_in') is-invalid @enderror"
                    value="{{ old('check_in', $guest->check_in ? \Carbon\Carbon::parse($guest->check_in)->format('Y-m-d') : '') }}">
                @error('check_in')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Check Out</label>
                <input type="date" name="check_out" class="form-control @error('check_out') is-invalid @enderror"
                    value="{{ old('check_out', $guest->check_out ? \Carbon\Carbon::parse($guest->check_out)->format('Y-m-d') : '') }}">
                @error('check_out')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Update Guest</button>
            <a href="{{ route('guests.show', $guest) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection