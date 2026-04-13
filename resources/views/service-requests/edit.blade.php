@extends('layouts.app')

@section('content')

@php $isAdmin = Auth::user()->role === 'admin'; @endphp

<div class="page-header">
    <h1>Update Request</h1>
    <p>Service request #{{ $serviceRequest->id }} — {{ $serviceRequest->guest->full_name ?? 'Unknown Guest' }}</p>
</div>

<div class="card" style="max-width:620px;">
    <form method="POST" action="{{ route('service-requests.update', $serviceRequest) }}">
        @csrf @method('PUT')

        @if($isAdmin)
        <div class="form-group">
            <label class="form-label" for="guest_id">Guest</label>
            <select id="guest_id" name="guest_id" class="form-control">
                <option value="">— No guest —</option>
                @foreach($guests as $guest)
                    <option value="{{ $guest->id }}" {{ old('guest_id', $serviceRequest->guest_id) == $guest->id ? 'selected' : '' }}>
                        {{ $guest->full_name }} (Room {{ $guest->room_number ?? '—' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="service_type">Service Type <span style="color:var(--rust)">*</span></label>
            <select id="service_type" name="service_type" class="form-control" required>
                @foreach(['Room Cleaning','Extra Towels / Linens','Room Service / Food','Maintenance / Repair','Luggage Assistance','Wake-up Call','Laundry Service','Transportation / Taxi','Concierge / Information','Other'] as $type)
                    <option value="{{ $type }}" {{ old('service_type', $serviceRequest->service_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('service_type')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="details">Details</label>
            <textarea id="details" name="details" class="form-control">{{ old('details', $serviceRequest->details) }}</textarea>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label" for="priority">Priority</label>
                <select id="priority" name="priority" class="form-control">
                    @foreach(['Normal','High','Urgent'] as $p)
                        <option value="{{ $p }}" {{ old('priority', $serviceRequest->priority) == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    @foreach(['Open','In Progress','Completed','Cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $serviceRequest->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @else
        {{-- Staff: status update only --}}
        <div style="background:var(--warm);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;">
            <p style="font-size:.88rem;color:var(--muted);margin-bottom:4px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Service Type</p>
            <p style="font-weight:600;">{{ $serviceRequest->service_type }}</p>
        </div>
        @if($serviceRequest->details)
        <div style="background:var(--warm);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;">
            <p style="font-size:.88rem;color:var(--muted);margin-bottom:4px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Details</p>
            <p>{{ $serviceRequest->details }}</p>
        </div>
        @endif
        <div class="form-group">
            <label class="form-label" for="status">Update Status <span style="color:var(--rust)">*</span></label>
            <select id="status" name="status" class="form-control" required>
                @foreach(['Open','In Progress','Completed','Cancelled'] as $s)
                    <option value="{{ $s }}" {{ old('status', $serviceRequest->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="form-actions">
            <button type="submit" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20,6 9,17 4,12"/></svg>
                Save Changes
            </button>
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">← Back</a>
        </div>
    </form>
</div>

@endsection