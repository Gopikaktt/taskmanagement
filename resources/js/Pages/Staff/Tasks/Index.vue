<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import StaffLayout from '@/Layouts/StaffLayout.vue';

const props = defineProps({
    tasks: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const priority = ref(props.filters?.priority || '');

const applyFilters = () => {
    router.get('/staff/tasks', {
        search: search.value || undefined,
        status: status.value || undefined,
        priority: priority.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    priority.value = '';
    router.get('/staff/tasks');
};

const updateTaskStatus = (task, newStatus) => {
    router.patch(`/staff/tasks/${task.id}/status`, {
        status: newStatus
    }, {
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">My Assigned Tasks</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Review task specifications, deliverables, and update your status.</p>
            </div>

            <!-- Spatie Permission Check: Create Task Button -->
            <div v-if="can('create tasks')">
                <Link href="/staff/tasks/create" style="display: inline-flex; align-items: center; gap: 6px; background: #10b981; color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">
                    <i class="fa-solid fa-plus"></i> + Create New Task
                </Link>
            </div>
        </div>

        <!-- Filter Bar -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div style="flex: 2; min-width: 220px;">
                <input v-model="search" 
                       @keyup.enter="applyFilters"
                       type="text" 
                       placeholder="Search tasks by title or instructions..." 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; outline: none;">
            </div>

            <div style="min-width: 140px;">
                <select v-model="status" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white; outline: none;">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <select v-model="priority" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white; outline: none;">
                    <option value="">All Priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <button @click="applyFilters" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            <button v-if="search || status || priority" @click="clearFilters" style="background: transparent; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                <i class="fa-solid fa-xmark"></i> Clear
            </button>
        </div>

        <!-- Tasks Table Card -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 12px 16px;">Task Name</th>
                        <th style="padding: 12px 16px;">Project</th>
                        <th style="padding: 12px 16px;">Priority</th>
                        <th style="padding: 12px 16px;">Due Date</th>
                        <th style="padding: 12px 16px;">Status</th>
                        <th style="padding: 12px 16px; text-align: right;">Quick Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks.data" :key="task.id" style="border-bottom: 1px solid #f1f5f9;">
                        <!-- Task Name -->
                        <td style="padding: 14px 16px;">
                            <Link :href="`/staff/tasks/${task.id}`" style="font-weight: 600; color: #0f172a; text-decoration: none; font-size: 0.95rem; display: block;">
                                {{ task.title }}
                            </Link>
                            <span v-if="task.is_overdue" style="color: #ef4444; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                <i class="fa-solid fa-triangle-exclamation"></i> Overdue
                            </span>
                        </td>

                        <!-- Project -->
                        <td style="padding: 14px 16px; color: #64748b; font-size: 0.9rem;">
                            {{ task.project?.name ?? 'None' }}
                        </td>

                        <!-- Priority Badge -->
                        <td style="padding: 14px 16px;">
                            <span :style="{
                                background: priorityBadgeColor(task.priority).bg,
                                color: priorityBadgeColor(task.priority).text,
                                padding: '3px 9px',
                                borderRadius: '9999px',
                                fontSize: '0.75rem',
                                fontWeight: '700',
                                textTransform: 'capitalize'
                            }">
                                {{ task.priority }}
                            </span>
                        </td>

                        <!-- Due Date -->
                        <td style="padding: 14px 16px; font-size: 0.875rem;" :style="{ color: task.is_overdue ? '#ef4444' : '#64748b', fontWeight: task.is_overdue ? '700' : 'normal' }">
                            {{ task.due_date ? task.due_date : 'No deadline' }}
                        </td>

                        <!-- Status Badge -->
                        <td style="padding: 14px 16px;">
                            <span :style="{
                                background: statusBadgeColor(task.status).bg,
                                color: statusBadgeColor(task.status).text,
                                padding: '3px 9px',
                                borderRadius: '9999px',
                                fontSize: '0.75rem',
                                fontWeight: '700',
                                textTransform: 'capitalize'
                            }">
                                {{ task.status.replace('_', ' ') }}
                            </span>
                        </td>

                        <!-- Quick Action -->
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; align-items: center; gap: 8px;">
                                <!-- Spatie Permission Check for Editing -->
                                <select v-if="can('edit tasks')" 
                                        :value="task.status" 
                                        @change="updateTaskStatus(task, $event.target.value)" 
                                        style="padding: 5px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.8rem; background: white; cursor: pointer;">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>

                                <Link :href="`/staff/tasks/${task.id}`" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 5px 10px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-eye"></i> Details
                                </Link>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="tasks.data.length === 0">
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 3rem; font-size: 0.95rem;">
                            No assigned tasks match your search filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Inertia Pagination Links -->
        <div v-if="tasks.links && tasks.links.length > 3" style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 4px;">
            <template v-for="(link, i) in tasks.links" :key="i">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      :style="{
                          padding: '6px 12px',
                          borderRadius: '6px',
                          fontSize: '0.85rem',
                          textDecoration: 'none',
                          fontWeight: link.active ? '700' : '500',
                          background: link.active ? '#10b981' : 'white',
                          color: link.active ? 'white' : '#475569',
                          border: '1px solid #e2e8f0'
                      }" />
                <span v-else
                      v-html="link.label"
                      style="padding: 6px 12px; color: #cbd5e1; font-size: 0.85rem; border: 1px solid #f1f5f9; border-radius: 6px; background: white;" />
            </template>
        </div>
    </StaffLayout>
</template>
