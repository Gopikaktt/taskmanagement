<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    project: Object,
    staffMembers: Array
});

const deleteProject = () => {
    if (confirm('Are you sure you want to delete this project and all its tasks?')) {
        router.delete(`/admin/projects/${props.project.id}`);
    }
};

const statusBadgeColor = (s) => {
    switch (s) {
        case 'completed': return { bg: '#dcfce7', text: '#15803d' };
        case 'in_progress': return { bg: '#e0f2fe', text: '#0284c7' };
        case 'on_hold': return { bg: '#fef3c7', text: '#b45309' };
        default: return { bg: '#f1f5f9', text: '#64748b' };
    }
};

const priorityBadgeColor = (p) => {
    switch (p) {
        case 'urgent': return { bg: '#fee2e2', text: '#ef4444' };
        case 'high': return { bg: '#ffedd5', text: '#f97316' };
        case 'medium': return { bg: '#fef3c7', text: '#d97706' };
        default: return { bg: '#e0f2fe', text: '#0284c7' };
    }
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <Link href="/admin/projects" style="color: #64748b; text-decoration: none; font-size: 0.875rem;">
                        <i class="fa-solid fa-arrow-left"></i> Projects
                    </Link>
                    <span style="color: #cbd5e1;">&bull;</span>
                    <span :style="{
                        background: statusBadgeColor(project.status).bg,
                        color: statusBadgeColor(project.status).text,
                        padding: '2px 8px',
                        borderRadius: '9999px',
                        fontSize: '0.75rem',
                        fontWeight: '700',
                        textTransform: 'capitalize'
                    }">
                        {{ project.status.replace('_', ' ') }}
                    </span>
                </div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">{{ project.name }}</h1>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <Link :href="`/admin/tasks/create?project_id=${project.id}`" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-plus"></i> Add Task
                </Link>
                <Link :href="`/admin/projects/${project.id}/edit`" style="background: white; border: 1px solid #cbd5e1; color: #334155; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-pen"></i> Edit Project
                </Link>
                <button @click="deleteProject" style="background: #fef2f2; border: 1px solid #fecaca; color: #ef4444; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </div>
        </div>

        <!-- Overview Card -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
                <div>
                    <h3 style="margin-top: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Scope &amp; Description</h3>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0; white-space: pre-line;">
                        {{ project.description || 'No detailed instructions provided.' }}
                    </p>
                </div>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem;">
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem;">
                            <span style="color: #64748b;">Overall Progress</span>
                            <span style="font-weight: 700; color: #4f46e5;">{{ project.progress }}%</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
                            <div :style="{ width: project.progress + '%', background: '#4f46e5', height: '100%' }"></div>
                        </div>
                    </div>

                    <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Start Date:</span>
                            <span style="font-weight: 500;">{{ project.start_date || 'Not set' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Target Deadline:</span>
                            <span style="font-weight: 600;">{{ project.end_date || 'Not set' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Created By:</span>
                            <span style="font-weight: 500;">{{ project.creator?.name || 'Administrator' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Tasks List -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #1e293b;">
                    Project Tasks ({{ project.tasks?.length || 0 }})
                </h3>
            </div>

            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.8rem; color: #64748b; text-transform: uppercase;">
                        <th style="padding: 10px 14px;">Task Title</th>
                        <th style="padding: 10px 14px;">Assigned To</th>
                        <th style="padding: 10px 14px;">Priority</th>
                        <th style="padding: 10px 14px;">Due Date</th>
                        <th style="padding: 10px 14px;">Status</th>
                        <th style="padding: 10px 14px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in project.tasks" :key="t.id" style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 14px; font-weight: 600; font-size: 0.9rem;">
                            <Link :href="`/admin/tasks/${t.id}`" style="color: #0f172a; text-decoration: none;">
                                {{ t.title }}
                            </Link>
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
                        <td style="padding: 12px 14px; font-size: 0.85rem; color: #64748b;">
                            {{ t.due_date || 'No deadline' }}
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
                                Details &rarr;
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="!project.tasks || project.tasks.length === 0">
                        <td colspan="6" style="padding: 2rem; text-align: center; color: #94a3b8;">
                            No tasks associated with this project.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
