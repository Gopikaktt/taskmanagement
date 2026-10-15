@extends('layouts.base')

@section('navbar')
<nav class="navbar">
    <div class="nav-container">
        <a href="{{ route('admin.dashboard') }}" class="brand-logo">
            <div class="brand-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <span>TaskFlow <span style="font-size: 0.75rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 9999px; margin-left: 6px; font-weight: 600;">ADMIN</span></span>
        </a>

        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie" style="margin-right: 4px;"></i> Dashboard
            </a>
            <a href="{{ route('admin.projects.index') }}" class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-tree" style="margin-right: 4px;"></i> Projects
            </a>
            <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check" style="margin-right: 4px;"></i> Tasks
            </a>
            <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-group" style="margin-right: 4px;"></i> Staff Members
            </a>
        </div>

        <div class="nav-user">
            <div class="user-badge">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span>{{ Auth::guard('admin')->user()->name ?? 'Administrator' }}</span>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" title="Log out">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>
@endsection
