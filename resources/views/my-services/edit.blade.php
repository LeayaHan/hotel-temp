@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Edit Request</h1>
    <p>Update your service request #{{ $serviceRequest->id }}.</p>
</div>

<div class="card" style="max-width:620px;">
    <form method="POST" action="{{ route('my-services.update', $serviceRequest) }}">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label" for="service_type">Service Type <span style="color:var(--rust)">*</span></label>
            <select id="service_type" name="service_type" class="form-control" required>
                @foreach([
                    'Room Cleaning','Extra Towels / Linens','Room Service / Food',
                    'Maintenance / Repair','Luggage Assistance','Wake-up Call',
                    'Laundry Service','Transportation / Taxi','Concierge / Information',
                    'Other'
                ] as $type)
                    <option value="{{ $type }}" {{ old('service_type', $serviceRequest->service_type) == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('service_type')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="priority">Priority</label>
            <select id="priority" name="priority" class="form-control">
                @foreach(['Normal','High','Urgent'] as $p)
                    <option value="{{ $p }}" {{ old('priority', $serviceRequest->priority) == $p ? 'selected' : '' }}>
                        {{ $p }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="details">Additional Details</label>
            <textarea id="details" name="details" class="form-control"
                      placeholder="Any specific instructions…">{{ old('details', $serviceRequest->details) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20,6 9,17 4,12"/></svg>
                Save Changes
            </button>
            <a href="{{ route('my-services.show', $serviceRequest) }}" class="btn btn-secondary">← Cancel</a>
        </div>
    </form>
</div>

@endsection