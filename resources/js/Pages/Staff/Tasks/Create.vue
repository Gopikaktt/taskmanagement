<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import StaffLayout from '@/Layouts/StaffLayout.vue';

const props = defineProps({
    projects: Array
});

const form = useForm({
    title: '',
    project_id: '',
    priority: 'medium',
    due_date: '',
    description: '',
});

const submit = () => {
    form.post('/staff/tasks');
};
</script>

<template>
    <StaffLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Create New Task</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Add a new task item under your active projects.</p>
            </div>
            <Link href="/staff/tasks" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Tasks
            </Link>
        </div>

        <!-- Form Card -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2rem; max-width: 720px; margin: 0 auto; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <form @submit.prevent="submit">
                <!-- Task Title -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                        Task Title <span style="color: #ef4444;">*</span>
                    </label>
                    <input v-model="form.title" 
                           type="text" 
                           placeholder="e.g. Implement authentication flows"
                           required
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;">
                    <div v-if="form.errors.title" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                        {{ form.errors.title }}
                    </div>
                </div>

                <!-- Project & Priority Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Project <span style="color: #ef4444;">*</span>
                        </label>
                        <select v-model="form.project_id" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white; box-sizing: border-box; outline: none;">
                            <option value="">-- Select Project --</option>
                            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                        <div v-if="form.errors.project_id" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                            {{ form.errors.project_id }}
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Priority <span style="color: #ef4444;">*</span>
                        </label>
                        <select v-model="form.priority" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white; box-sizing: border-box; outline: none;">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <div v-if="form.errors.priority" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                            {{ form.errors.priority }}
                        </div>
                    </div>
                </div>

                <!-- Target Due Date -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                        Target Due Date
                    </label>
                    <input v-model="form.due_date" 
                           type="date" 
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;">
                    <div v-if="form.errors.due_date" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                        {{ form.errors.due_date }}
                    </div>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                        Task Instructions / Deliverables
                    </label>
                    <textarea v-model="form.description" 
                              rows="4" 
                              placeholder="Detail the task scope and requirements..."
                              style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;"></textarea>
                    <div v-if="form.errors.description" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                        {{ form.errors.description }}
                    </div>
                </div>

                <!-- Actions -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <Link href="/staff/tasks" style="background: #f1f5f9; color: #475569; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
                        Cancel
                    </Link>
                    <button type="submit" 
                            :disabled="form.processing"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 10px 22px; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">
                        <i class="fa-solid fa-plus"></i> {{ form.processing ? 'Saving...' : 'Create Task' }}
                    </button>
                </div>
            </form>
        </div>
    </StaffLayout>
</template>
