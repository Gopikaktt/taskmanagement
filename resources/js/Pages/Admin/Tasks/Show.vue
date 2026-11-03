<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    task: Object
});

const deleteTask = () => {
    if (confirm(`Are you sure you want to delete task "${props.task.title}"?`)) {
        router.delete(`/admin/tasks/${props.task.id}`);
    }
};

const statusBadgeColor = (s) => {
    switch (s) {
        case 'completed': return { bg: '#dcfce7', text: '#15803d', border: '#bbf7d0' };
        case 'in_progress': return { bg: '#e0f2fe', text: '#0284c7', border: '#bae6fd' };
        case 'cancelled': return { bg: '#fee2e2', text: '#b91c1c', border: '#fecaca' };
        default: return { bg: '#fef3c7', text: '#b45309', border: '#fde68a' }; // pending
    }
};

const priorityBadgeColor = (p) => {
    switch (p) {
        case 'urgent': return { bg: '#ffe4e6', text: '#e11d48', border: '#fecdd3' };
        case 'high': return { bg: '#ffedd5', text: '#ea580c', border: '#fed7aa' };
        case 'medium': return { bg: '#fef9c3', text: '#a16207', border: '#fef08a' };
        default: return { bg: '#f1f5f9', text: '#475569', border: '#e2e8f0' }; // low
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return 'No deadline';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return 'Incomplete';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <Link href="/admin/tasks" style="color: #64748b; font-size: 0.875rem; text-decoration: none;">
                        <i class="fa-solid fa-arrow-left"></i> Tasks
                    </Link>
                    <span style="color: #cbd5e1;">&bull;</span>
                    <span :style="{
                        background: priorityBadgeColor(task.priority).bg,
                        color: priorityBadgeColor(task.priority).text,
                        border: '1px solid ' + priorityBadgeColor(task.priority).border,
                        padding: '2px 8px',
                        borderRadius: '9999px',
                        fontSize: '0.75rem',
                        fontWeight: '700',
                        textTransform: 'capitalize'
                    }">
                        {{ task.priority }}
                    </span>
                    <span :style="{
                        background: statusBadgeColor(task.status).bg,
                        color: statusBadgeColor(task.status).text,
                        border: '1px solid ' + statusBadgeColor(task.status).border,
                        padding: '2px 8px',
                        borderRadius: '9999px',
                        fontSize: '0.75rem',
                        fontWeight: '700',
                        textTransform: 'capitalize'
                    }">
                        {{ task.status.replace('_', ' ') }}
                    </span>
                </div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">{{ task.title }}</h1>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <Link :href="`/admin/tasks/${task.id}/edit`" style="background: white; border: 1px solid #cbd5e1; color: #334155; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-pen"></i> Edit Task
                </Link>
                <button @click="deleteTask" style="background: #fee2e2; border: 1px solid #fecaca; color: #b91c1c; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </div>
        </div>

        <!-- Main Content & Sidebar Grid -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <!-- Left: Description & Specs -->
            <div>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem;">Description &amp; Specifications</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.7; white-space: pre-line; margin: 0;">
                        {{ task.description || 'No detailed description provided for this task.' }}
                    </p>
                </div>
            </div>

            <!-- Right: Task Metadata Sidebar -->
            <div>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                        Task Metadata
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 1.25rem; font-size: 0.875rem;">
                        <!-- Project -->
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Project</div>
                            <div style="margin-top: 4px;">
                                <Link v-if="task.project" :href="`/admin/projects/${task.project.id}`" style="color: #4f46e5; text-decoration: none; font-weight: 600;">
                                    {{ task.project.name }}
                                </Link>
                                <span v-else style="color: #94a3b8;">None</span>
                            </div>
                        </div>

                        <!-- Assigned Staff -->
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Assigned Staff</div>
                            <div style="margin-top: 4px;">
                                <div v-if="task.assigned_staff" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #4f46e5; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                                        {{ task.assigned_staff.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #0f172a;">{{ task.assigned_staff.name }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ task.assigned_staff.email }}</div>
                                    </div>
                                </div>
                                <span v-else style="color: #94a3b8; font-style: italic;">Unassigned</span>
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Due Date</div>
                            <div style="margin-top: 4px; font-weight: 600;" :style="{ color: task.is_overdue ? '#dc2626' : '#0f172a' }">
                                {{ formatDate(task.due_date) }}
                                <span v-if="task.is_overdue" style="background: #fee2e2; color: #b91c1c; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; margin-left: 6px;">Overdue</span>
                            </div>
                        </div>

                        <!-- Completed At -->
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Completed At</div>
                            <div style="margin-top: 4px; color: #334155;">
                                {{ formatDateTime(task.completed_at) }}
                            </div>
                        </div>

                        <!-- Created By -->
                        <div>
                            <div style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Created By</div>
                            <div style="margin-top: 4px; color: #334155; font-weight: 500;">
                                {{ task.creator?.name || 'Administrator' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
