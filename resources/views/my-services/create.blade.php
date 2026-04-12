@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>New Service Request</h1>
    <p>Fill in the details below and our team will attend to you shortly.</p>
</div>

<div class="card" style="max-width:620px;">
    <form method="POST" action="{{ route('my-services.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="service_type">Service Type <span style="color:var(--rust)">*</span></label>
            <select id="service_type" name="service_type" class="form-control" required>
                <option value="" disabled {{ old('service_type') ? '' : 'selected' }}>Select a service…</option>
                @foreach([
                    'Room Cleaning','Extra Towels / Linens','Room Service / Food',
                    'Maintenance / Repair','Luggage Assistance','Wake-up Call',
                    'Laundry Service','Transportation / Taxi','Concierge / Information',
                    'Other'
                ] as $type)
                    <option value="{{ $type }}" {{ old('service_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('service_type')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="priority">Priority</label>
            <select id="priority" name="priority" class="form-control">
                <option value="Normal" {{ old('priority','Normal') == 'Normal' ? 'selected' : '' }}>Normal</option>
                <option value="High"   {{ old('priority') == 'High'   ? 'selected' : '' }}>High</option>
                <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="details">Additional Details</label>
            <textarea id="details" name="details" class="form-control"
                      placeholder="Any specific instructions or information…">{{ old('details') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20,6 9,17 4,12"/></svg>
                Submit Request
            </button>
            <a href="{{ route('my-services.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection