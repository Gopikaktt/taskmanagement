<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div style="min-height: 100vh; background-color: var(--bg-body, #f8fafc); font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <!-- Top Navbar -->
        <nav style="background: white; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;">
            <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: center; align-items: center;">
                <Link href="/" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #0f172a; font-weight: 700; font-size: 1.25rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.25);">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span>TaskFlow</span>
                </Link>
            </div>
        </nav>

        <!-- Main Content -->
        <main style="max-width: 760px; margin: 4rem auto; padding: 0 1.5rem; text-align: center;">
            <div style="margin-bottom: 3rem;">
                <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; color: #0f172a; margin-bottom: 0.5rem;">
                    Task Management System
                </h1>
                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">
                    Please choose your portal to sign in
                </p>
            </div>

            <!-- Two Portal Selection Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; text-align: left;">
                <!-- Admin Portal Option -->
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    <div>
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #ffffff; margin-bottom: 1.25rem; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h2 style="font-size: 1.35rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; margin-top: 0;">
                            Administrator
                        </h2>
                        <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.75rem;">
                            Manage projects, oversee staff personnel, and create or assign tasks across teams.
                        </p>
                    </div>

                    <Link href="/admin/login" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Admin Login &rarr;
                    </Link>
                </div>

                <!-- Staff Portal Option -->
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    <div>
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #ffffff; margin-bottom: 1.25rem; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <h2 style="font-size: 1.35rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; margin-top: 0;">
                            Staff Member
                        </h2>
                        <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.75rem;">
                            Access your assigned tasks, review requirements, and update task deliverables.
                        </p>
                    </div>

                    <Link href="/staff/login" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">
                        <i class="fa-solid fa-right-to-bracket"></i> Staff Login &rarr;
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>
