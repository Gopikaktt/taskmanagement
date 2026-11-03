<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    staff: Object,
    departments: Array,
    filters: Object
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const department = ref(props.filters?.department || '');

const applyFilters = () => {
    router.get('/admin/staff', {
        search: search.value || undefined,
        status: status.value || undefined,
        department: department.value || undefined
    }, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    department.value = '';
    router.get('/admin/staff');
};

const toggleStatus = (member) => {
    router.patch(`/admin/staff/${member.id}/toggle-status`, {}, {
        preserveScroll: true
    });
};

const toggleVerification = (member) => {
    router.patch(`/admin/staff/${member.id}/toggle-verification`, {}, {
        preserveScroll: true
    });
};

const deleteMember = (member) => {
    if (confirm(`Are you sure you want to remove staff member "${member.name}"?`)) {
        router.delete(`/admin/staff/${member.id}`, {
            preserveScroll: true
        });
    }
};

const roleBadgeColor = (roleName) => {
    switch (roleName) {
        case 'manager': return { bg: '#fdf2f8', text: '#db2777', border: '#fbcfe8' };
        case 'team-lead': return { bg: '#eff6ff', text: '#2563eb', border: '#bfdbfe' };
        case 'member': return { bg: '#f0fdf4', text: '#16a34a', border: '#bbf7d0' };
        default: return { bg: '#f1f5f9', text: '#64748b', border: '#e2e8f0' };
    }
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Staff Members</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Manage personnel accounts, eligibility verification, and department assignments.</p>
            </div>
            <Link href="/admin/staff/create" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                <i class="fa-solid fa-user-plus"></i> Add Staff Member
            </Link>
        </div>

        <!-- Filter Bar -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div style="flex: 2; min-width: 200px;">
                <input v-model="search" 
                       @keyup.enter="applyFilters"
                       type="text" 
                       placeholder="Search staff by name, email, department..." 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; outline: none;">
            </div>

            <div style="min-width: 160px;">
                <select v-model="status" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: white; outline: none;">
                    <option value="">All Account Statuses</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Deactivated Only</option>
                </select>
            </div>

            <div style="min-width: 160px;">
                <select v-model="department" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; background: white; outline: none;">
                    <option value="">All Departments</option>
                    <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
                </select>
            </div>

            <button @click="applyFilters" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            <button v-if="search || status || department" @click="clearFilters" style="background: transparent; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                <i class="fa-solid fa-xmark"></i> Clear
            </button>
        </div>

        <!-- Staff Table -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 12px 16px;">Staff Member</th>
                            <th style="padding: 12px 16px;">Role</th>
                            <th style="padding: 12px 16px;">Department</th>
                            <th style="padding: 12px 16px;">Contact</th>
                            <th style="padding: 12px 16px;">Account Status</th>
                            <th style="padding: 12px 16px;">Email Verification</th>
                            <th style="padding: 12px 16px;">Tasks</th>
                            <th style="padding: 12px 16px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in staff.data" :key="member.id" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
                                        {{ member.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0f172a;">{{ member.name }}</div>
                                        <div style="font-size: 0.8rem; color: #64748b;">{{ member.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <div v-if="member.roles && member.roles.length > 0" style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    <span v-for="r in member.roles" :key="r.id" :style="{
                                        background: roleBadgeColor(r.name).bg,
                                        color: roleBadgeColor(r.name).text,
                                        border: '1px solid ' + roleBadgeColor(r.name).border,
                                        padding: '2px 8px',
                                        borderRadius: '9999px',
                                        fontSize: '0.725rem',
                                        fontWeight: '700',
                                        textTransform: 'capitalize'
                                    }">
                                        {{ r.name.replace('-', ' ') }}
                                    </span>
                                </div>
                                <span v-else style="color: #94a3b8; font-size: 0.8rem; font-style: italic;">Standard</span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <span style="color: #475569; font-size: 0.85rem;">
                                    {{ member.department || 'Unassigned' }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <span style="color: #475569; font-size: 0.85rem;">
                                    {{ member.phone || '-' }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <button @click="toggleStatus(member)" 
                                        :style="{
                                            background: member.is_active ? '#dcfce7' : '#fee2e2',
                                            color: member.is_active ? '#15803d' : '#b91c1c',
                                            border: '1px dashed ' + (member.is_active ? '#86efac' : '#fca5a5'),
                                            padding: '4px 10px',
                                            borderRadius: '9999px',
                                            fontSize: '0.75rem',
                                            fontWeight: '700',
                                            cursor: 'pointer',
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            gap: '5px'
                                        }"
                                        title="Click to toggle account status">
                                    <span :style="{ width: '6px', height: '6px', borderRadius: '50%', background: member.is_active ? '#16a34a' : '#dc2626' }"></span>
                                    {{ member.is_active ? 'Active' : 'Deactivated' }}
                                </button>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <button @click="toggleVerification(member)" 
                                        :style="{
                                            background: member.email_verified_at ? '#e0f2fe' : '#fef3c7',
                                            color: member.email_verified_at ? '#0369a1' : '#b45309',
                                            border: '1px dashed ' + (member.email_verified_at ? '#7dd3fc' : '#fcd34d'),
                                            padding: '4px 10px',
                                            borderRadius: '9999px',
                                            fontSize: '0.75rem',
                                            fontWeight: '700',
                                            cursor: 'pointer',
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            gap: '5px'
                                        }"
                                        title="Click to toggle email verification">
                                    <span :style="{ width: '6px', height: '6px', borderRadius: '50%', background: member.email_verified_at ? '#0284c7' : '#d97706' }"></span>
                                    {{ member.email_verified_at ? 'Verified' : 'Unverified' }}
                                </button>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle;">
                                <Link :href="`/admin/tasks?staff_id=${member.id}`" style="font-weight: 600; color: #4f46e5; text-decoration: none;">
                                    {{ member.tasks_count }} Tasks
                                </Link>
                            </td>
                            <td style="padding: 14px 16px; vertical-align: middle; text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <Link :href="`/admin/staff/${member.id}/edit`" style="background: #f1f5f9; color: #475569; width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="Edit Staff Member">
                                        <i class="fa-solid fa-pen" style="font-size: 0.8rem;"></i>
                                    </Link>
                                    <button @click="deleteMember(member)" style="background: #fee2e2; border: none; color: #ef4444; width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;" title="Delete Staff Member">
                                        <i class="fa-solid fa-trash" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!staff.data || staff.data.length === 0">
                            <td colspan="8" style="text-align: center; color: #94a3b8; padding: 3rem 1rem;">
                                <i class="fa-solid fa-users" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 0.5rem; display: block;"></i>
                                No staff members match the criteria.
                                <div style="margin-top: 0.75rem;">
                                    <Link href="/admin/staff/create" style="color: #4f46e5; font-weight: 600; text-decoration: underline;">Add a staff member</Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="staff.links && staff.links.length > 3" style="display: flex; justify-content: center; gap: 4px; margin-top: 2rem;">
            <Link v-for="(link, i) in staff.links" 
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
