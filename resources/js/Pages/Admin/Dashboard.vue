<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: Object,
    recentTasks: Array,
    recentProjects: Array,
    staffMembers: Array,
    firstOverdueTask: Object
});

const priorityBadgeColor = (p) => {
    switch (p) {
        case 'urgent': return { bg: '#fee2e2', text: '#ef4444' };
        case 'high': return { bg: '#ffedd5', text: '#f97316' };
        case 'medium': return { bg: '#fef3c7', text: '#d97706' };
        default: return { bg: '#e0f2fe', text: '#0284c7' };
    }
};

const statusBadgeColor = (s) => {
    switch (s) {
        case 'completed': return { bg: '#dcfce7', text: '#15803d' };
        case 'in_progress': return { bg: '#e0f2fe', text: '#0284c7' };
        case 'cancelled': return { bg: '#f1f5f9', text: '#64748b' };
        default: return { bg: '#fef3c7', text: '#b45309' };
    }
};

const overdueUrl = () => {
    if (props.stats?.overdue_tasks === 1 && props.firstOverdueTask) {
        return `/admin/tasks/${props.firstOverdueTask.id}`;
    } else if (props.stats?.overdue_tasks > 0) {
        return '/admin/tasks?overdue=1';
    }
    return '/admin/tasks';
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Executive Dashboard</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Real-time oversight of projects, staff allocation, and task progress.</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <Link href="/admin/projects/create" style="background: white; border: 1px solid #cbd5e1; color: #334155; padding: 9px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-folder-plus"></i> New Project
                </Link>
                <Link href="/admin/tasks/create" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                    <i class="fa-solid fa-plus"></i> Create Task
                </Link>
            </div>
        </div>

        <!-- 4 KPI Stat Cards -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
            <!-- Projects -->
            <Link href="/admin/projects" style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: block;">
                <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Total Projects</div>
                <div style="font-size: 2rem; font-weight: 800; color: #0f172a; margin: 4px 0;">{{ stats.total_projects }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">
                    <span style="color: #0284c7; font-weight: 700;">{{ stats.active_projects }}</span> in progress / active
                </div>
            </Link>

            <!-- Staff -->
            <Link href="/admin/staff" style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: block;">
                <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Staff Members</div>
                <div style="font-size: 2rem; font-weight: 800; color: #0f172a; margin: 4px 0;">{{ stats.total_staff }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">
                    <span style="color: #059669; font-weight: 700;">{{ stats.active_staff }}</span> active personnel
                </div>
            </Link>

            <!-- Tasks -->
            <Link href="/admin/tasks" style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: block;">
                <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Total Tasks</div>
                <div style="font-size: 2rem; font-weight: 800; color: #0f172a; margin: 4px 0;">{{ stats.total_tasks }}</div>
                <div style="font-size: 0.8rem; color: #64748b;">
                    <span style="color: #d97706; font-weight: 700;">{{ stats.in_progress_tasks }}</span> in progress
                </div>
            </Link>

            <!-- Overdue -->
            <Link :href="overdueUrl()" style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: block;">
                <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Overdue Tasks</div>
                <div style="font-size: 2rem; font-weight: 800; margin: 4px 0;" :style="{ color: stats.overdue_tasks > 0 ? '#ef4444' : '#0f172a' }">
                    {{ stats.overdue_tasks }}
                </div>
                <div style="font-size: 0.8rem; color: #64748b;">
                    <span style="color: #15803d; font-weight: 700;">{{ stats.completed_tasks }}</span> completed
                </div>
            </Link>
        </div>

        <!-- 2-Column: Recent Tasks & Project Overview -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <!-- Left: Recent Tasks -->
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-list-check" style="color: #4f46e5;"></i> Recent Tasks
                    </h3>
                    <Link href="/admin/tasks" style="color: #4f46e5; font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                        View All Tasks &rarr;
                    </Link>
                </div>

                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">
                            <th style="padding: 10px 14px;">Task</th>
                            <th style="padding: 10px 14px;">Project</th>
                            <th style="padding: 10px 14px;">Assigned To</th>
                            <th style="padding: 10px 14px;">Priority</th>
                            <th style="padding: 10px 14px;">Status</th>
                            <th style="padding: 10px 14px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in recentTasks" :key="t.id" style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px 14px; font-weight: 600; font-size: 0.9rem;">
                                <Link :href="`/admin/tasks/${t.id}`" style="color: #0f172a; text-decoration: none;">
                                    {{ t.title }}
                                </Link>
                            </td>
                            <td style="padding: 12px 14px; font-size: 0.85rem; color: #64748b;">
                                {{ t.project?.name ?? 'None' }}
                            </td>
                            <td style="padding: 12px 14px; font-size: 0.85rem; color: #334155;">
                                {{ t.assigned_staff?.name ?? 'Unassigned' }}
                            </td>
                            <td style="padding: 12px 14px;">
                                <span :style="{
                                    background: priorityBadgeColor(t.priority).bg,
                                    color: priorityBadgeColor(t.priority).text,
                                    padding: '2px 8px',
                                    borderRadius: '9999px',
                                    fontSize: '0.75rem',
                                    fontWeight: '700',
                                    textTransform: 'capitalize'
                                }">
                                    {{ t.priority }}
                                </span>
                            </td>
                            <td style="padding: 12px 14px;">
                                <span :style="{
                                    background: statusBadgeColor(t.status).bg,
                                    color: statusBadgeColor(t.status).text,
                                    padding: '2px 8px',
                                    borderRadius: '9999px',
                                    fontSize: '0.75rem',
                                    fontWeight: '700',
                                    textTransform: 'capitalize'
                                }">
                                    {{ t.status.replace('_', ' ') }}
                                </span>
                            </td>
                            <td style="padding: 12px 14px; text-align: right;">
                                <Link :href="`/admin/tasks/${t.id}`" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 0.8rem;">
                                    View
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="recentTasks.length === 0">
                            <td colspan="6" style="padding: 2rem; text-align: center; color: #94a3b8;">No tasks found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Right: Active Projects -->
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b;">
                        Active Projects
                    </h3>
                    <Link href="/admin/projects" style="color: #4f46e5; font-size: 0.85rem; font-weight: 600; text-decoration: none;">
                        All &rarr;
                    </Link>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div v-for="p in recentProjects" :key="p.id" style="border: 1px solid #f1f5f9; padding: 12px; border-radius: 8px; background: #fafafa;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <Link :href="`/admin/projects/${p.id}`" style="font-weight: 600; color: #0f172a; text-decoration: none; font-size: 0.9rem;">
                                {{ p.name }}
                            </Link>
                            <span style="font-size: 0.75rem; background: #e0f2fe; color: #0284c7; padding: 1px 6px; border-radius: 4px; font-weight: 600;">
                                {{ p.tasks_count }} tasks
                            </span>
                        </div>
                        <div style="font-size: 0.8rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ p.description || 'No description provided.' }}
                        </div>
                    </div>

                    <div v-if="recentProjects.length === 0" style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 1.5rem;">
                        No projects created yet.
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
