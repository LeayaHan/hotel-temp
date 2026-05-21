@extends('layouts.app')

@section('content')

<div class="page-header">
    <h1>Edit Account</h1>
    <p>Editing account for <strong>{{ $user->name }}</strong></p>
</div>

<div class="card" style="max-width:680px;">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Account Type <span style="color:var(--rust);">*</span></label>
            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror">
                <option value="customer"   {{ old('role', $user->role) === 'customer'   ? 'selected' : '' }}>🛎 Guest (Customer)</option>
                <option value="front_desk" {{ old('role', $user->role) === 'front_desk' ? 'selected' : '' }}>🖥 Front Desk Staff</option>
                <option value="staff"      {{ old('role', $user->role) === 'staff'      ? 'selected' : '' }}>🧹 Housekeeping Staff</option>
                <option value="manager"    {{ old('role', $user->role) === 'manager'    ? 'selected' : '' }}>📋 Manager</option>
                <option value="admin"      {{ old('role', $user->role) === 'admin'      ? 'selected' : '' }}>🔑 Administrator</option>
            </select>
            @error('role')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Full Name <span style="color:var(--rust);">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                       class="form-control @error('name') is-invalid @enderror"
                       autocomplete="off">
                <p id="name-error" style="display:none;color:var(--rust);font-size:.8rem;margin-top:4px;">Numbers are not allowed in the name field.</p>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address <span style="color:var(--rust);">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                   placeholder="e.g. 09123456789"
                   maxlength="11"
                   class="form-control @error('phone') is-invalid @enderror"
                   inputmode="numeric">
            <p id="phone-error" style="display:none;color:var(--rust);font-size:.8rem;margin-top:4px;">Phone number must be exactly 11 digits.</p>
            @error('phone')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group" id="department-field">
            <label class="form-label">Department</label>
            <input type="text" name="department" value="{{ old('department', $user->department) }}"
                   placeholder="e.g. Housekeeping, Front Office"
                   class="form-control @error('department') is-invalid @enderror">
            @error('department')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">
                    New Password
                    <span style="color:var(--muted);font-weight:400;text-transform:none;">(leave blank to keep current)</span>
                </label>
                <input type="password" name="password"
                       placeholder="Min. 8 characters"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       placeholder="Re-enter new password"
                       class="form-control">
            </div>
        </div>

        <div class="form-group" style="display:flex;align-items:center;gap:10px;">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                   style="width:16px;height:16px;accent-color:var(--gold);cursor:pointer;">
            <label for="is_active" style="font-size:.88rem;color:var(--ink);cursor:pointer;margin:0;">
                Account is active (user can log in)
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save Changes</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    // ── Role / Department toggle ──
    const roleSelect = document.getElementById('role');
    const deptField  = document.getElementById('department-field');
    function toggleDept() {
        const staffRoles = ['staff', 'front_desk', 'manager', 'admin'];
        deptField.style.display = staffRoles.includes(roleSelect.value) ? 'block' : 'none';
    }
    roleSelect.addEventListener('change', toggleDept);
    toggleDept();

    // ── Name field: block numbers ──
    const nameInput  = document.getElementById('name');
    const nameError  = document.getElementById('name-error');
    nameInput.addEventListener('keypress', function (e) {
        if (/[0-9]/.test(e.key)) {
            e.preventDefault();
            nameError.style.display = 'block';
        }
    });
    nameInput.addEventListener('input', function () {
        // Strip any digits that were pasted in
        const cleaned = this.value.replace(/[0-9]/g, '');
        if (cleaned !== this.value) {
            this.value = cleaned;
            nameError.style.display = 'block';
        } else {
            nameError.style.display = 'none';
        }
    });

    // ── Phone field: digits only, 11-digit limit ──
    const phoneInput = document.getElementById('phone');
    const phoneError = document.getElementById('phone-error');
    phoneInput.addEventListener('keypress', function (e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });
    phoneInput.addEventListener('input', function () {
        // Strip non-digits (handles paste)
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
    });
    phoneInput.addEventListener('blur', function () {
        if (this.value.length > 0 && this.value.length !== 11) {
            phoneError.style.display = 'block';
        } else {
            phoneError.style.display = 'none';
        }
    });
    phoneInput.addEventListener('focus', function () {
        phoneError.style.display = 'none';
    });
</script>

@endsection