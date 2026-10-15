@extends('layouts.base')

@section('navbar')
<nav class="navbar">
    <div class="nav-container">
        <a href="{{ route('staff.dashboard') }}" class="brand-logo">
            <div class="brand-icon" style="background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <span>TaskFlow <span style="font-size: 0.75rem; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 9999px; margin-left: 6px; font-weight: 600;">STAFF</span></span>
        </a>

        <div class="nav-links">
            <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge" style="margin-right: 4px;"></i> Overview
            </a>
            <a href="{{ route('staff.tasks.index') }}" class="nav-link {{ request()->routeIs('staff.tasks.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check" style="margin-right: 4px;"></i> My Tasks
            </a>
        </div>

        <div class="nav-user">
            <div class="user-badge">
                <div class="user-avatar" style="background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);">
                    {{ strtoupper(substr(Auth::guard('staff')->user()->name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight: 600;">{{ Auth::guard('staff')->user()->name ?? 'Staff Member' }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ Auth::guard('staff')->user()->department ?? 'Staff' }}</div>
                </div>
            </div>

            <form action="{{ route('staff.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" title="Log out">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>
@endsection
