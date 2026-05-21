@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Edit Request</h1>
    <p>Update your service request #{{ $serviceRequest->id }}.</p>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('my-services.update', $serviceRequest) }}" id="serviceForm">
        @csrf @method('PUT')

        {{-- ── LOCATION ── --}}
        <div style="background:var(--warm);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;margin-bottom:24px;">
            <div style="font-size:.78rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:14px;">
                📍 Your Location
            </div>
            <div class="form-grid-2">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="floor">Floor</label>
                    <input type="text" id="floor" name="floor"
                        class="form-control"
                        value="{{ $serviceRequest->floor }}"
                        readonly
                        style="background:var(--warm);color:var(--muted);cursor:not-allowed;">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="room_number">Room Number</label>
                    <input type="text" id="room_number" name="room_number"
                        class="form-control"
                        value="{{ $serviceRequest->room_number }}"
                        readonly
                        style="background:var(--warm);color:var(--muted);cursor:not-allowed;">
                </div>
            </div>
            <p style="font-size:.78rem;color:var(--muted);margin-top:10px;margin-bottom:0;">
                🔒 Your floor and room are assigned by the front desk.
            </p>
        </div>

        {{-- ── SERVICE TYPE ── --}}
        <div class="form-group">
            <label class="form-label" for="service_type">Service Type <span style="color:var(--rust)">*</span></label>
            <select id="service_type" name="service_type" class="form-control" required onchange="handleServiceChange(this.value)">
                @foreach([
                    'Room Cleaning', 'Extra Towels / Linens', 'Room Service / Food',
                    'Maintenance / Repair', 'Luggage Assistance', 'Wake-up Call',
                    'Laundry Service', 'Toiletries', 'Transportation / Taxi',
                    'Concierge / Information', 'Other'
                ] as $type)
                    <option value="{{ $type }}" {{ old('service_type', $serviceRequest->service_type) == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('service_type')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        {{-- ── QUANTITY SECTIONS ── --}}
        @foreach($serviceItems as $service => $items)
        @php
            $savedQty = is_array($serviceRequest->quantities) ? $serviceRequest->quantities : [];
        @endphp
        <div id="qty_section_{{ Str::slug($service, '_') }}" class="qty-section" style="display:none;">
            <div style="margin-bottom:10px;">
                <div class="form-label" style="margin-bottom:2px;">Item Quantities</div>
                <p style="font-size:.83rem;color:var(--muted);margin:0;">Enter how many of each item you need. Leave blank to skip.</p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px 20px;background:var(--warm);border:1px solid var(--border);border-radius:var(--radius);padding:18px;margin-bottom:20px;">
                @foreach($items as $item)
                @php
                    $key        = 'qty_' . Str::slug($item, '_');
                    $oldVal     = old($key);
                    $savedVal   = $savedQty[$item] ?? 0;
                    $displayVal = $oldVal !== null ? $oldVal : strval($savedVal);
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                    <label style="font-size:.88rem;color:var(--ink);font-weight:500;flex:1;">{{ $item }}</label>
                    <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:7px;overflow:hidden;background:#fff;flex-shrink:0;">
                        <button type="button" onclick="changeQty('{{ $key }}', -1)"
                            style="width:32px;height:32px;border:none;background:transparent;font-size:1.2rem;cursor:pointer;color:var(--muted);line-height:1;display:flex;align-items:center;justify-content:center;">−</button>
                        <input type="text" inputmode="numeric" pattern="[0-9]*"
                            id="{{ $key }}" name="{{ $key }}"
                            value="{{ $displayVal }}"
                            style="width:40px;height:32px;border:none;border-left:1px solid var(--border);border-right:1px solid var(--border);text-align:center;font-size:.9rem;font-family:'DM Sans',sans-serif;color:var(--ink);background:#fff;outline:none;">
                        <button type="button" onclick="changeQty('{{ $key }}', 1)"
                            style="width:32px;height:32px;border:none;background:transparent;font-size:1.2rem;cursor:pointer;color:var(--muted);line-height:1;display:flex;align-items:center;justify-content:center;">+</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- ── PRIORITY ── --}}
        <div class="form-group">
            <label class="form-label">Priority <span style="color:var(--rust)">*</span></label>
            <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px;">

                <label id="priority_urgent" style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:1.5px solid var(--border);border-radius:9px;cursor:pointer;transition:border-color .15s,background .15s;"
                    onclick="selectPriority('Urgent')">
                    <input type="radio" name="priority" value="Urgent"
                        {{ old('priority', $serviceRequest->priority) == 'Urgent' ? 'checked' : '' }}
                        style="margin-top:3px;accent-color:var(--rust);flex-shrink:0;">
                    <div>
                        <div style="font-weight:600;font-size:.92rem;color:var(--rust);">🚨 Urgent</div>
                        <div style="font-size:.82rem;color:var(--muted);margin-top:2px;">Needs attention as soon as possible.</div>
                    </div>
                </label>

                <label id="priority_scheduled" style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:1.5px solid var(--border);border-radius:9px;cursor:pointer;transition:border-color .15s,background .15s;"
                    onclick="selectPriority('Scheduled')">
                    <input type="radio" name="priority" value="Scheduled"
                        {{ old('priority', $serviceRequest->priority) == 'Scheduled' ? 'checked' : '' }}
                        style="margin-top:3px;accent-color:var(--gold);flex-shrink:0;">
                    <div style="width:100%;">
                        <div style="font-weight:600;font-size:.92rem;color:var(--ink);">🗓 Scheduled</div>
                        <div style="font-size:.82rem;color:var(--muted);margin-top:2px;">Planned for a specific date and time.</div>
                        <div id="scheduled_at_wrap" style="display:{{ old('priority', $serviceRequest->priority) == 'Scheduled' ? 'block' : 'none' }};margin-top:12px;">
                            <label class="form-label" style="font-size:.78rem;">Date & Time Needed</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                class="form-control @error('scheduled_at') is-invalid @enderror"
                                value="{{ old('scheduled_at', $serviceRequest->scheduled_at ? $serviceRequest->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                style="max-width:280px;">
                            @error('scheduled_at')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </label>

                <label id="priority_low" style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;border:1.5px solid var(--border);border-radius:9px;cursor:pointer;transition:border-color .15s,background .15s;"
                    onclick="selectPriority('Low')">
                    <input type="radio" name="priority" value="Low"
                        {{ old('priority', $serviceRequest->priority) == 'Low' ? 'checked' : '' }}
                        style="margin-top:3px;accent-color:var(--success);flex-shrink:0;">
                    <div>
                        <div style="font-weight:600;font-size:.92rem;color:var(--success);">✅ Low</div>
                        <div style="font-size:.82rem;color:var(--muted);margin-top:2px;">Not urgent, can wait.</div>
                    </div>
                </label>

            </div>
            @error('priority')<div class="form-error" style="margin-top:6px;">{{ $message }}</div>@enderror
        </div>

        {{-- ── ADDITIONAL DETAILS ── --}}
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

<script>
const quantityServices = @json($quantityServices);

function slugify(str) {
    return str.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
}

function handleServiceChange(value) {
    document.querySelectorAll('.qty-section').forEach(el => el.style.display = 'none');
    if (quantityServices.includes(value)) {
        const section = document.getElementById('qty_section_' + slugify(value));
        if (section) section.style.display = 'block';
    }
}

function changeQty(id, delta) {
    const input = document.getElementById(id);
    const current = parseInt(input.value) || 0;
    const newVal = Math.max(0, Math.min(99, current + delta));
    input.value = String(newVal);
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.qty-section input[inputmode="numeric"]').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value === '') this.value = '0';
            const n = parseInt(this.value);
            if (n > 99) this.value = '99';
        });
        input.addEventListener('focus', function () {
            if (this.value === '0') this.select();
        });
    });

    const serviceSelect = document.getElementById('service_type');
    if (serviceSelect.value) handleServiceChange(serviceSelect.value);

    const checkedPriority = document.querySelector('input[name="priority"]:checked');
    if (checkedPriority) selectPriority(checkedPriority.value);
});

function selectPriority(value) {
    const wrap = document.getElementById('scheduled_at_wrap');
    wrap.style.display = value === 'Scheduled' ? 'block' : 'none';

    ['Urgent','Scheduled','Low'].forEach(p => {
        const el = document.getElementById('priority_' + p.toLowerCase());
        if (!el) return;
        if (p === value) {
            el.style.borderColor = p === 'Urgent' ? 'var(--rust)' : p === 'Scheduled' ? 'var(--gold)' : 'var(--success)';
            el.style.background  = p === 'Urgent' ? '#fff5f5'     : p === 'Scheduled' ? '#fefce8'     : '#f0fdf4';
        } else {
            el.style.borderColor = 'var(--border)';
            el.style.background  = 'transparent';
        }
    });
}
</script>

@endsection