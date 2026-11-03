<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    project: Object
});

const form = useForm({
    name: props.project.name,
    description: props.project.description || '',
    status: props.project.status,
    start_date: props.project.start_date || '',
    end_date: props.project.end_date || '',
});

const submit = () => {
    form.put(`/admin/projects/${props.project.id}`);
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Edit Project: {{ project.name }}</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Update project timelines, status, and description.</p>
            </div>
            <Link :href="`/admin/projects/${project.id}`" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Project
            </Link>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2rem; max-width: 720px; margin: 0 auto; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <form @submit.prevent="submit">
                <!-- Project Name -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                        Project Name <span style="color: #ef4444;">*</span>
                    </label>
                    <input v-model="form.name" 
                           type="text" 
                           required 
                           style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;">
                    <div v-if="form.errors.name" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Description -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                        Description
                    </label>
                    <textarea v-model="form.description" 
                              rows="3" 
                              style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;"></textarea>
                    <div v-if="form.errors.description" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                        {{ form.errors.description }}
                    </div>
                </div>

                <!-- Status, Start Date, End Date Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Project Status <span style="color: #ef4444;">*</span>
                        </label>
                        <select v-model="form.status" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white; box-sizing: border-box; outline: none;">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                        <div v-if="form.errors.status" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                            {{ form.errors.status }}
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Start Date
                        </label>
                        <input v-model="form.start_date" 
                               type="date" 
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;">
                        <div v-if="form.errors.start_date" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                            {{ form.errors.start_date }}
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            End / Due Date
                        </label>
                        <input v-model="form.end_date" 
                               type="date" 
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;">
                        <div v-if="form.errors.end_date" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                            {{ form.errors.end_date }}
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <Link :href="`/admin/projects/${project.id}`" style="background: #f1f5f9; color: #475569; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
                        Cancel
                    </Link>
                    <button type="submit" 
                            :disabled="form.processing"
                            style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; border: none; padding: 10px 22px; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                        <i class="fa-solid fa-check"></i> {{ form.processing ? 'Saving...' : 'Update Project' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
