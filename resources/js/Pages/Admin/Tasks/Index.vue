<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tasks: Object,
    projects: Array,
    staffMembers: Array,
    filters: Object
});

const search = ref(props.filters?.search || '');
const project_id = ref(props.filters?.project_id || '');
const staff_id = ref(props.filters?.staff_id || '');
const priority = ref(props.filters?.priority || '');
const status = ref(props.filters?.status || '');
const overdue = ref(props.filters?.overdue || '');

const applyFilters = () => {
    router.get('/admin/tasks', {
        search: search.value || undefined,
        project_id: project_id.value || undefined,
        staff_id: staff_id.value || undefined,
        priority: priority.value || undefined,
        status: status.value || undefined,
        overdue: overdue.value ? '1' : undefined
    }, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    search.value = '';
    project_id.value = '';
    staff_id.value = '';
    priority.value = '';
    status.value = '';
    overdue.value = '';
    router.get('/admin/tasks');
};

const deleteTask = (taskId, taskTitle) => {
    if (confirm(`Are you sure you want to delete task "${taskTitle}"?`)) {
        router.delete(`/admin/tasks/${taskId}`);
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
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Tasks Management</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Assign work, schedule deadlines, and monitor completion progress.</p>
            </div>
            <Link href="/admin/tasks/create" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                <i class="fa-solid fa-plus"></i> Create Task
            </Link>
        </div>

        <!-- Filter Bar -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div style="flex: 2; min-width: 180px;">
                <input v-model="search" 
                       @keyup.enter="applyFilters"
                       type="text" 
                       placeholder="Search tasks..." 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; outline: none;">
            </div>

            <div style="flex: 1; min-width: 140px;">
                <select v-model="project_id" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: white; outline: none;">
                    <option value="">All Projects</option>
                    <option v-for="proj in projects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 140px;">
                <select v-model="staff_id" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: white; outline: none;">
                    <option value="">All Staff</option>
                    <option v-for="member in staffMembers" :key="member.id" :value="member.id">{{ member.name }}</option>
                </select>
            </div>

            <div style="min-width: 120px;">
                <select v-model="priority" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: white; outline: none;">
                    <option value="">All Priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <div style="min-width: 120px;">
                <select v-model="status" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: white; outline: none;">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <button @click="applyFilters" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            <button v-if="search || project_id || staff_id || priority || status || overdue" @click="clearFilters" style="background: transparent; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                <i class="fa-solid fa-xmark"></i> Clear
            </button>
        </div>

        <!-- Tasks Table -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 12px 16px;">Task Title</th>
                            <th style="padding: 12px 16px;">Project</th>
                            <th style="padding: 12px 16px;">Assigned Staff</th>
                            <th style="padding: 12px 16px;">Priority</th>
                            <th style="padding: 12px 16px;">Due Date</th>
                            <th style="padding: 12px 16px;">Status</th>
                            <th style="padding: 12px 16px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: divide-gray-200;">
                        <tr v-for="task in tasks.data" :key="task.id" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <Link :href="`/admin/tasks/${task.id}`" style="font-weight: 600; color: #0f172a; text-decoration: none; display: block;">
                                    {{ task.title }}
                                </Link>
                                <span v-if="task.is_overdue" style="font-size: 0.725rem; color: #dc2626; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; margin-top: 3px;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Overdue
                                </span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <Link v-if="task.project" :href="`/admin/projects/${task.project.id}`" style="color: #4f46e5; text-decoration: none; font-weight: 500;">
                                    {{ task.project.name }}
                                </Link>
                                <span v-else style="color: #94a3b8; font-size: 0.8rem;">None</span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <div v-if="task.assigned_staff" style="display: inline-flex; align-items: center; gap: 6px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 3px 8px; border-radius: 9999px; font-size: 0.775rem; font-weight: 600;">
                                    <i class="fa-solid fa-user-check" style="font-size: 0.7rem;"></i>
                                    <span>{{ task.assigned_staff.name }}</span>
                                </div>
                                <span v-else style="color: #94a3b8; font-style: italic; font-size: 0.8rem;">Unassigned</span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <span :style="{
                                    background: priorityBadgeColor(task.priority).bg,
                                    color: priorityBadgeColor(task.priority).text,
                                    border: '1px solid ' + priorityBadgeColor(task.priority).border,
                                    padding: '3px 8px',
                                    borderRadius: '9999px',
                                    fontSize: '0.725rem',
                                    fontWeight: '700',
                                    textTransform: 'capitalize'
                                }">
                                    {{ task.priority }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <span :style="{ color: task.is_overdue ? '#dc2626' : '#475569', fontWeight: task.is_overdue ? '700' : '500', fontSize: '0.85rem' }">
                                    {{ formatDate(task.due_date) }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <span :style="{
                                    background: statusBadgeColor(task.status).bg,
                                    color: statusBadgeColor(task.status).text,
                                    border: '1px solid ' + statusBadgeColor(task.status).border,
                                    padding: '3px 8px',
                                    borderRadius: '9999px',
                                    fontSize: '0.725rem',
                                    fontWeight: '700',
                                    textTransform: 'capitalize'
                                }">
                                    {{ task.status.replace('_', ' ') }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle; text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <Link :href="`/admin/tasks/${task.id}`" style="background: #f1f5f9; color: #475569; width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="View Details">
                                        <i class="fa-solid fa-eye" style="font-size: 0.8rem;"></i>
                                    </Link>
                                    <Link :href="`/admin/tasks/${task.id}/edit`" style="background: #f1f5f9; color: #475569; width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="Edit Task">
                                        <i class="fa-solid fa-pen" style="font-size: 0.8rem;"></i>
                                    </Link>
                                    <button @click="deleteTask(task.id, task.title)" style="background: #fee2e2; border: none; color: #ef4444; width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;" title="Delete Task">
                                        <i class="fa-solid fa-trash" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!tasks.data || tasks.data.length === 0">
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 3rem 1rem;">
                                <i class="fa-solid fa-clipboard-list" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 0.5rem; display: block;"></i>
                                No tasks found matching your criteria.
                                <div style="margin-top: 0.75rem;">
                                    <Link href="/admin/tasks/create" style="color: #4f46e5; font-weight: 600; text-decoration: underline;">Create a new task</Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="tasks.links && tasks.links.length > 3" style="display: flex; justify-content: center; gap: 4px; margin-top: 2rem;">
            <Link v-for="(link, i) in tasks.links" 
                  :key="i"
                  :href="link.url || '#'" 
                  v-html="link.label"
                  :style="{
                      padding: '8px 14px',
                      borderRadius: '6px',
                      fontSize: '0.85rem',
                      textDecoration: 'none',
                      background: link.active ? '#4f46e5' : 'white',
                      color: link.active ? 'white' : (link.url ? '#334155' : '#94a3b8'),
                      border: '1px solid ' + (link.active ? '#4f46e5' : '#e2e8f0'),
                      cursor: link.url ? 'pointer' : 'default'
                  }" />
        </div>
    </AdminLayout>
</template>
