<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import StaffLayout from '@/Layouts/StaffLayout.vue';

const props = defineProps({
    task: Object
});

const form = useForm({
    status: props.task.status
});

const updateStatus = () => {
    form.patch(`/staff/tasks/${props.task.id}/status`, {
        preserveScroll: true
    });
};

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
</script>

<template>
    <StaffLayout>
        <!-- Page Header -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <Link href="/staff/tasks" style="color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-arrow-left"></i> My Tasks
                </Link>
                <span style="color: #cbd5e1;">&bull;</span>
                <span :style="{
                    background: priorityBadgeColor(task.priority).bg,
                    color: priorityBadgeColor(task.priority).text,
                    padding: '2px 8px',
                    borderRadius: '4px',
                    fontSize: '0.75rem',
                    fontWeight: '700',
                    textTransform: 'capitalize'
                }">
                    {{ task.priority }}
                </span>
                <span :style="{
                    background: statusBadgeColor(task.status).bg,
                    color: statusBadgeColor(task.status).text,
                    padding: '2px 8px',
                    borderRadius: '4px',
                    fontSize: '0.75rem',
                    fontWeight: '700',
                    textTransform: 'capitalize'
                }">
                    {{ task.status.replace('_', ' ') }}
                </span>
            </div>
            <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">{{ task.title }}</h1>
        </div>

        <!-- 2 Column Layout -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <!-- Left Column: Requirements & Project -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Description Card -->
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h3 style="margin-top: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">
                        Requirements &amp; Instructions
                    </h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.7; margin: 0; white-space: pre-line;">
                        {{ task.description || 'No detailed instructions provided.' }}
                    </p>
                </div>

                <!-- Project Info Card -->
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h3 style="margin-top: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-folder" style="color: #0284c7;"></i> Project: {{ task.project?.name ?? 'General Project' }}
                    </h3>
                    <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        {{ task.project?.description || 'No project overview description provided.' }}
                    </p>
                </div>
            </div>

            <!-- Right Column: Status Update & Metadata -->
            <div>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h3 style="margin-top: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                        Update Task Status
                    </h3>

                    <!-- Spatie Permission Check: Edit Status -->
                    <form v-if="can('edit tasks')" @submit.prevent="updateStatus">
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 6px;">
                                Change Status
                            </label>
                            <select v-model="form.status" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white; outline: none; box-sizing: border-box;">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" 
                                :disabled="form.processing"
                                style="width: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">
                            <i class="fa-solid fa-check"></i> {{ form.processing ? 'Saving...' : 'Save Status' }}
                        </button>
                    </form>
                    <div v-else style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 6px; color: #64748b; font-size: 0.85rem;">
                        You have read-only access to this task.
                    </div>

                    <!-- Metadata Details -->
                    <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.85rem;">
                        <div>
                            <span style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Due Date</span>
                            <div :style="{ fontWeight: '600', color: task.is_overdue ? '#ef4444' : '#1e293b' }">
                                {{ task.due_date ? task.due_date : 'No deadline' }}
                                <span v-if="task.is_overdue" style="background: #fee2e2; color: #ef4444; font-size: 0.7rem; padding: 1px 6px; border-radius: 4px; margin-left: 6px;">
                                    Overdue
                                </span>
                            </div>
                        </div>

                        <div>
                            <span style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Assigned By</span>
                            <div style="font-weight: 600; color: #1e293b;">
                                {{ task.creator?.name || 'Administrator' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StaffLayout>
</template>
