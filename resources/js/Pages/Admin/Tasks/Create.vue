<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    projects: Array,
    staffMembers: Array,
    selectedProjectId: [Number, String]
});

const form = useForm({
    title: '',
    project_id: props.selectedProjectId || '',
    staff_id: '',
    description: '',
    priority: 'medium',
    status: 'pending',
    due_date: ''
});

const submit = () => {
    form.post('/admin/tasks');
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Create &amp; Assign Task</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Schedule work item, define priorities, and allocate to active staff.</p>
            </div>
            <Link href="/admin/tasks" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Tasks
            </Link>
        </div>

        <!-- Form Card -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2rem; max-width: 760px; margin: 0 auto; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <form @submit.prevent="submit">
                <!-- Task Title -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                        Task Title <span style="color: #ef4444;">*</span>
                    </label>
                    <input v-model="form.title" 
                           type="text" 
                           required 
                           placeholder="e.g. Implement OAuth SSO Integration" 
                           style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                    <div v-if="form.errors.title" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.title }}</div>
                </div>

                <!-- Project & Staff Assignment Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Associated Project <span style="color: #ef4444;">*</span>
                        </label>
                        <select v-model="form.project_id" 
                                required 
                                style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: white; outline: none; box-sizing: border-box;">
                            <option value="">-- Select Project --</option>
                            <option v-for="proj in projects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
                        </select>
                        <div v-if="form.errors.project_id" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.project_id }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Assign to Staff Member
                        </label>
                        <select v-model="form.staff_id" 
                                style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: white; outline: none; box-sizing: border-box;">
                            <option value="">-- Unassigned (Allocate Later) --</option>
                            <option v-for="member in staffMembers" :key="member.id" :value="member.id">
                                {{ member.name }} ({{ member.department || 'General' }})
                            </option>
                        </select>
                        <div v-if="form.errors.staff_id" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.staff_id }}</div>
                    </div>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                        Task Description
                    </label>
                    <textarea v-model="form.description" 
                              rows="4" 
                              placeholder="Provide technical specifications, acceptance criteria, or context..." 
                              style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; resize: vertical; box-sizing: border-box;"></textarea>
                    <div v-if="form.errors.description" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.description }}</div>
                </div>

                <!-- Priority, Status, Due Date Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 2rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Priority <span style="color: #ef4444;">*</span>
                        </label>
                        <select v-model="form.priority" 
                                required 
                                style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: white; outline: none; box-sizing: border-box;">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <div v-if="form.errors.priority" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.priority }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Initial Status <span style="color: #ef4444;">*</span>
                        </label>
                        <select v-model="form.status" 
                                required 
                                style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: white; outline: none; box-sizing: border-box;">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <div v-if="form.errors.status" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.status }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Due Date
                        </label>
                        <input v-model="form.due_date" 
                               type="date" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: white; outline: none; box-sizing: border-box;">
                        <div v-if="form.errors.due_date" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.due_date }}</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <Link href="/admin/tasks" style="padding: 10px 18px; border: 1px solid #cbd5e1; border-radius: 6px; color: #475569; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center;">
                        Cancel
                    </Link>
                    <button type="submit" 
                            :disabled="form.processing"
                            style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 10px 24px; border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                        <i class="fa-solid fa-plus"></i>
                        <span>{{ form.processing ? 'Creating...' : 'Create &amp; Assign Task' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
