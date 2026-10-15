@extends('layouts.base')

@section('title', 'Task Management System')

@section('navbar')
<nav class="navbar">
    <div class="nav-container" style="justify-content: center;">
        <a href="{{ route('home') }}" class="brand-logo">
            <div class="brand-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <span>TaskFlow</span>
        </a>
    </div>
</nav>
@endsection

@section('content')
<div style="max-width: 720px; margin: 4rem auto; text-align: center;">
    <div style="margin-bottom: 2.5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; color: var(--text-primary); margin-bottom: 0.5rem;">
            Task Management System
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.1rem;">
            Please choose your portal to sign in
        </p>
    </div>

    <!-- Two Portal Selection Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; text-align: left;">
        <!-- Admin Portal Option -->
        <div class="card" style="padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid var(--border-subtle); transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div>
                <div style="width: 52px; height: 52px; border-radius: 14px; background: var(--brand-gradient); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #ffffff; margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    Administrator
                </h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.75rem;">
                    Manage projects, oversee staff personnel, and create or assign tasks.
                </p>
            </div>

            <a href="{{ route('admin.login') }}" class="btn btn-primary" style="width: 100%; padding: 0.75rem 1rem; font-size: 0.95rem;">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Admin Login &rarr;
            </a>
        </div>

        <!-- Staff Portal Option -->
        <div class="card" style="padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid var(--border-subtle); transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div>
                <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #ffffff; margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    Staff Member
                </h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.75rem;">
                    Access your assigned tasks, review requirements, and update task progress.
                </p>
            </div>

            <a href="{{ route('staff.login') }}" class="btn btn-primary" style="width: 100%; padding: 0.75rem 1rem; font-size: 0.95rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Staff Login &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
