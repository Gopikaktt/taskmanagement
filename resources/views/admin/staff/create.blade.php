@extends('layouts.admin')

@section('title', 'Add Staff Member - TaskFlow')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Add Staff Member</h1>
        <p class="page-subtitle">Provision account credentials and department affiliation for new staff.</p>
    </div>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Staff List
    </a>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form action="{{ route('admin.staff.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="name" class="form-label">Full Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email <span style="color: #ef4444;">*</span></label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Temporary Password <span style="color: #ef4444;">*</span></label>
            <input type="password" id="password" name="password" class="form-control" value="{{ old('password') }}" required>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="department" class="form-label">Department</label>
                <input type="text" id="department" name="department" class="form-control" value="{{ old('department') }}">
                @error('department')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                @error('phone')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="background: var(--bg-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem; margin-top: 0.5rem; margin-bottom: 1.5rem;">
            <label class="form-check" style="margin-bottom: 0.75rem; cursor: pointer;">
                <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <div>
                    <span style="font-weight: 600; color: var(--text-primary);">Active Account</span>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">Inactive staff cannot sign in or view assigned tasks.</div>
                </div>
            </label>

            <label class="form-check" style="cursor: pointer;">
                <input type="checkbox" name="is_verified" class="form-check-input" value="1" {{ old('is_verified', true) ? 'checked' : '' }}>
                <div>
                    <span style="font-weight: 600; color: var(--text-primary);">Email Verified</span>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">Unverified staff will be prompted to complete email verification before accessing tasks.</div>
                </div>
            </label>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Save Staff Member
            </button>
        </div>
    </form>
</div>
@endsection
