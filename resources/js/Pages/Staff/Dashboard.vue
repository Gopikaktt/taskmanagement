<script setup>
import { Link } from '@inertiajs/vue3';
import StaffLayout from '@/Layouts/StaffLayout.vue';

// 1. Receive the props sent from DashboardController.php
defineProps({
    staff: Object,
    stats: Object,
    recentTasks: Array
});
</script>

<template>
    <StaffLayout>
        <div class="dashboard-container">

        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: bold; margin: 0;">
                    Welcome, {{ staff.name }}

                    <!-- SPATIE ROLE CHECK IN VUE: -->
                    <!-- John Doe has 'manager' -> shows Purple Badge -->
                    <!-- Sarah Smith does NOT have 'manager' -> shows Grey Badge -->
                    <span v-if="hasRole('manager')" style="background: #7c3aed; color: white; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; margin-left: 8px;">
                        ★ MANAGER
                    </span>
                    <span v-else-if="hasRole('team-lead')" style="background: #2563eb; color: white; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; margin-left: 8px;">
                        ★ TEAM LEAD
                    </span>
                    <span v-else style="background: #94a3b8; color: white; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; margin-left: 8px;">
                        STAFF
                    </span>
                </h1>
                <p style="color: #64748b; margin-top: 4px;">Track your assignments and progress.</p>
            </div>

            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <!-- SPATIE PERMISSION CHECK FOR EXPORT REPORTS: -->
                <button v-if="can('export reports')" @click="alert('Report exported successfully!')" style="background: #3b82f6; color: white; padding: 8px 14px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);">
                    📊 Export Reports
                </button>

                <!-- SPATIE PERMISSION CHECK IN VUE: -->
                <!-- Only users with 'create tasks' permission see this button -->
                <div v-if="can('create tasks')">
                    <a href="/staff/tasks/create" style="display: inline-block; background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">
                        + Create New Task
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Grid (from DashboardController $stats) -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem;">
                <div style="color: #64748b; font-size: 0.85rem;">Total Assigned</div>
                <div style="font-size: 1.75rem; font-weight: bold;">{{ stats.total_assigned }}</div>
            </div>

            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem;">
                <div style="color: #0284c7; font-size: 0.85rem;">In Progress</div>
                <div style="font-size: 1.75rem; font-weight: bold; color: #0284c7;">{{ stats.in_progress }}</div>
            </div>

            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem;">
                <div style="color: #059669; font-size: 0.85rem;">Completed</div>
                <div style="font-size: 1.75rem; font-weight: bold; color: #059669;">{{ stats.completed }}</div>
            </div>

            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem;">
                <div style="color: #ef4444; font-size: 0.85rem;">Overdue</div>
                <div style="font-size: 1.75rem; font-weight: bold; color: #ef4444;">{{ stats.overdue }}</div>
            </div>
        </div>

        <!-- Recent Tasks Table (from DashboardController $recentTasks) -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem;">Your Active Tasks</h3>
                <a href="/staff/tasks" style="color: #0284c7; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                    View All Tasks &rarr;
                </a>
            </div>

            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 0.85rem;">
                        <th style="padding: 10px 8px;">Task Title</th>
                        <th style="padding: 10px 8px;">Project</th>
                        <th style="padding: 10px 8px;">Status</th>
                        <th style="padding: 10px 8px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in recentTasks" :key="task.id" style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 8px; font-weight: 600;">
                            <a :href="`/staff/tasks/${task.id}`" style="color: #1e293b; text-decoration: none;">
                                {{ task.title }}
                            </a>
                        </td>
                        <td style="padding: 12px 8px; color: #64748b;">{{ task.project?.name ?? 'No Project' }}</td>
                        <td style="padding: 12px 8px;">
                            <span style="background: #e0f2fe; color: #0284c7; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500;">
                                {{ task.status }}
                            </span>
                        </td>
                        <td style="padding: 12px 8px; text-align: right;">
                            <!-- SPATIE PERMISSION CHECK FOR EDITING -->
                            <a v-if="can('edit tasks')" :href="`/staff/tasks/${task.id}`" style="display: inline-block; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
                                Edit / Update Status
                            </a>
                            <a v-else :href="`/staff/tasks/${task.id}`" style="display: inline-block; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 0.8rem;">
                                View
                            </a>
                        </td>
                    </tr>
                    <tr v-if="recentTasks.length === 0">
                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 1.5rem;">
                            No tasks found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    </StaffLayout>
</template>
