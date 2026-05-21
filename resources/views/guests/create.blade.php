@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Add Guest</h1>
    <p>Register a new guest and assign them a room.</p>
</div>

<div class="card">
    <form method="POST" action="{{ route('guests.store') }}">
        @csrf

        {{-- Name Fields --}}
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">First Name <span style="color:var(--rust)">*</span></label>
                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror"
                    value="{{ old('first_name') }}" placeholder="e.g. Juan">
                @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Last Name <span style="color:var(--rust)">*</span></label>
                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror"
                    value="{{ old('last_name') }}" placeholder="e.g. dela Cruz">
                @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-group" style="max-width:200px;">
            <label class="form-label">Middle Initial <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
            <input type="text" name="middle_initial" id="middle_initial" class="form-control @error('middle_initial') is-invalid @enderror"
                value="{{ old('middle_initial') }}" placeholder="e.g. B" maxlength="5">
            @error('middle_initial')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Email <span style="color:var(--rust)">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="e.g. juan@email.com">
                <small style="color:var(--muted);font-size:.8rem;">A login account will be created using this email.</small>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}" placeholder="e.g. 09123456789" maxlength="11">
                @error('phone')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Floor Number</label>
                <select id="floor_number" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror">
                    <option value="">— Select floor —</option>
                    @foreach(range(1, 6) as $f)
                        <option value="{{ $f }}" {{ old('floor_number') == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
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
            const oldRoom  = '{{ old('room_number') }}';

            function populateRooms(floor, selected) {
                roomSel.innerHTML = '<option value="">— Select room —</option>';
                if (!floor) { roomSel.innerHTML = '<option value="">— Select floor first —</option>'; return; }
                for (let r = 1; r <= 10; r++) {
                    const num = floor + '0' + (r < 10 ? '0' + r : r);
                    // e.g. floor 2 => 201..210
                    const roomNum = parseInt(floor) * 100 + r;
                    const opt = document.createElement('option');
                    opt.value = roomNum;
                    opt.textContent = 'Room ' + roomNum;
                    if (String(roomNum) === String(selected)) opt.selected = true;
                    roomSel.appendChild(opt);
                }
            }

            floorSel.addEventListener('change', () => populateRooms(floorSel.value, ''));
            // On page load restore old value if validation failed
            if (floorSel.value) populateRooms(floorSel.value, oldRoom);
        })();
        </script>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Status <span style="color:var(--rust)">*</span></label>
                <select name="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="">— Select status —</option>
                    @foreach(['Reserved', 'Checked In', 'Checked Out'] as $option)
                        <option value="{{ $option }}" {{ old('status') === $option ? 'selected' : '' }}>
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
                    value="{{ old('check_in') }}">
                @error('check_in')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Check Out</label>
                <input type="date" name="check_out" class="form-control @error('check_out') is-invalid @enderror"
                    value="{{ old('check_out') }}">
                @error('check_out')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save Guest</button>
            <a href="{{ route('guests.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

{{-- Input restriction scripts --}}
<script>
(function () {
    // ── Letters-only fields (first name, last name, middle initial) ──
    // Allows: letters (a-z, A-Z), spaces, hyphens, apostrophes, dots
    const lettersOnly = /^[a-zA-ZÀ-ÿ\s'\-\.]*$/;

    ['first_name', 'last_name', 'middle_initial'].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener('keypress', function (e) {
            const char = String.fromCharCode(e.which || e.keyCode);
            if (!lettersOnly.test(char)) e.preventDefault();
        });

        el.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s'\-\.]/g, '');
        });

        el.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            this.value = pasted.replace(/[^a-zA-ZÀ-ÿ\s'\-\.]/g, '');
        });
    });

    // ── Numbers-only field (phone) — exactly 11 digits ──
    const phoneEl = document.getElementById('phone');
    if (phoneEl) {
        phoneEl.addEventListener('keypress', function (e) {
            const char = String.fromCharCode(e.which || e.keyCode);
            if (!/[0-9]/.test(char)) e.preventDefault();
            if (this.value.length >= 11) e.preventDefault();
        });

        phoneEl.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
        });

        phoneEl.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            this.value = pasted.replace(/[^0-9]/g, '').slice(0, 11);
        });
    }
})();
</script>

@endsection