<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    staff: Object,
    roles: Array
});

const currentRole = props.staff.roles && props.staff.roles.length > 0 ? props.staff.roles[0].name : '';

const form = useForm({
    name: props.staff.name,
    email: props.staff.email,
    password: '',
    role: currentRole,
    department: props.staff.department || '',
    phone: props.staff.phone || '',
    is_active: Boolean(props.staff.is_active),
    is_verified: Boolean(props.staff.email_verified_at),
});

const submit = () => {
    form.put(`/admin/staff/${props.staff.id}`);
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Edit Staff: {{ staff.name }}</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Update contact details, security credentials, and role privileges.</p>
            </div>
            <Link href="/admin/staff" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Back to Staff List
            </Link>
        </div>

        <!-- Form Card -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2rem; max-width: 680px; margin: 0 auto; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <form @submit.prevent="submit">
                <!-- Name & Email Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Full Name <span style="color: #ef4444;">*</span>
                        </label>
                        <input v-model="form.name" 
                               type="text" 
                               required 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                        <div v-if="form.errors.name" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Email Address <span style="color: #ef4444;">*</span>
                        </label>
                        <input v-model="form.email" 
                               type="email" 
                               required 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                        <div v-if="form.errors.email" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.email }}</div>
                    </div>
                </div>

                <!-- Password & Role Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            New Password <span style="color: #94a3b8; font-weight: 400; font-size: 0.8rem;">(Leave blank to keep existing)</span>
                        </label>
                        <input v-model="form.password" 
                               type="password" 
                               placeholder="••••••••" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                        <div v-if="form.errors.password" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Spatie Role Assignment
                        </label>
                        <select v-model="form.role" 
                                style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: white; outline: none; box-sizing: border-box;">
                            <option value="">No Special Role (Standard)</option>
                            <option v-for="r in roles" :key="r" :value="r">
                                {{ r.charAt(0).toUpperCase() + r.slice(1).replace('-', ' ') }}
                            </option>
                        </select>
                        <div v-if="form.errors.role" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.role }}</div>
                    </div>
                </div>

                <!-- Department & Phone Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Department
                        </label>
                        <input v-model="form.department" 
                               type="text" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                        <div v-if="form.errors.department" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.department }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Phone Number
                        </label>
                        <input v-model="form.phone" 
                               type="text" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; outline: none; box-sizing: border-box;">
                        <div v-if="form.errors.phone" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">{{ form.errors.phone }}</div>
                    </div>
                </div>

                <!-- Status Checkboxes -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.75rem;">
                    <label style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 1rem; cursor: pointer;">
                        <input v-model="form.is_active" type="checkbox" style="margin-top: 4px; width: 16px; height: 16px; accent-color: #4f46e5;">
                        <div>
                            <span style="font-weight: 600; color: #0f172a; font-size: 0.9rem;">Active Account</span>
                            <div style="font-size: 0.775rem; color: #64748b;">Deactivating immediately revokes portal access and task updating rights.</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                        <input v-model="form.is_verified" type="checkbox" style="margin-top: 4px; width: 16px; height: 16px; accent-color: #4f46e5;">
                        <div>
                            <span style="font-weight: 600; color: #0f172a; font-size: 0.9rem;">Email Verified</span>
                            <div style="font-size: 0.775rem; color: #64748b;">Verified email is required for staff to interact with work orders.</div>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <Link href="/admin/staff" style="padding: 10px 18px; border: 1px solid #cbd5e1; border-radius: 6px; color: #475569; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center;">
                        Cancel
                    </Link>
                    <button type="submit" 
                            :disabled="form.processing"
                            style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 10px 24px; border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ form.processing ? 'Saving...' : 'Save Changes' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
