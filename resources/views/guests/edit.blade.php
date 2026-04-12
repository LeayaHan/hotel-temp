@extends('layouts.app')

@section('content')
@if(Auth::user()->role !== 'staff' && Auth::user()->role !== 'admin')
    <div class="card"><p>Unauthorized.</p></div>
@else

<div class="card" style="max-width:680px;">
    <div class="page-header">
        <h2 style="margin:0;">Edit Guest</h2>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary btn-sm">← Back</a>
    </div>

    <form action="{{ route('guests.update', $guest) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div>
                <label>Full Name <span style="color:var(--danger)">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name', $guest->full_name) }}">
                @error('full_name') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div>
                <label>Email <span style="color:var(--danger)">*</span></label>
                <input type="email" name="email" value="{{ old('email', $guest->email) }}">
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $guest->phone) }}">
            </div>
            <div>
                <label>Room Number</label>
                <input type="text" name="room_number" value="{{ old('room_number', $guest->room_number) }}">
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Check In</label>
                <input type="date" name="check_in" value="{{ old('check_in', $guest->check_in) }}">
            </div>
            <div>
                <label>Check Out</label>
                <input type="date" name="check_out" value="{{ old('check_out', $guest->check_out) }}">
                @error('check_out') <div class="error-text">{{ $message }}</div> @enderror
            </div>
        </div>

        <label>Status</label>
        <select name="status">
            @foreach(['Pending','Reserved','Checked In','Checked Out'] as $s)
                <option value="{{ $s }}" {{ $guest->status == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn">Update Guest</button>
            <a href="{{ route('guests.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endif
@endsection