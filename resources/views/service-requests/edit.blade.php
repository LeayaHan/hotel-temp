@extends('layouts.app')

@section('content')
@if(Auth::user()->role !== 'staff' && Auth::user()->role !== 'admin')
    <div class="card"><p>Unauthorized.</p></div>
@else

<div class="card" style="max-width:680px;">
    <div class="page-header">
        <h2 style="margin:0;">Edit Service Request #{{ $serviceRequest->id }}</h2>
        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary btn-sm">← Back</a>
    </div>

    <form action="{{ route('service-requests.update', $serviceRequest) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Guest</label>
        <select name="guest_id">
            <option value="">— Select guest —</option>
            @foreach($guests as $guest)
                <option value="{{ $guest->id }}" {{ old('guest_id', $serviceRequest->guest_id) == $guest->id ? 'selected' : '' }}>
                    {{ $guest->full_name }} (Room {{ $guest->room_number }})
                </option>
            @endforeach
        </select>
        @error('guest_id') <div class="error-text">{{ $message }}</div> @enderror

        <label>Service Type <span style="color:var(--danger)">*</span></label>
        <select name="service_type">
            @foreach(['Room Cleaning','Laundry','Room Service','Maintenance','Extra Amenities','Wake-up Call','Transportation','Other'] as $type)
                <option value="{{ $type }}" {{ old('service_type', $serviceRequest->service_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
        @error('service_type') <div class="error-text">{{ $message }}</div> @enderror

        <label>Details / Notes</label>
        <textarea name="details">{{ old('details', $serviceRequest->details) }}</textarea>

        <label>Priority</label>
        <select name="priority">
            @foreach(['Normal','Urgent','Low'] as $p)
                <option value="{{ $p }}" {{ old('priority', $serviceRequest->priority) == $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>

        <label>Status</label>
        <select name="status">
            @foreach(['Open','In Progress','Completed','Cancelled'] as $s)
                <option value="{{ $s }}" {{ old('status', $serviceRequest->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>

        <div style="display:flex; gap:10px; margin-top:8px;">
            <button type="submit" class="btn">Update Request</button>
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endif
@endsection