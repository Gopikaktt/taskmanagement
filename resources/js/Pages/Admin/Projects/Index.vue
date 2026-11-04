<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    projects: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

const applyFilters = () => {
    router.get('/admin/projects', {
        search: search.value || undefined,
        status: status.value || undefined
    }, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    router.get('/admin/projects');
};

const statusBadgeColor = (s) => {
    switch (s) {
        case 'completed': return { bg: '#dcfce7', text: '#15803d' };
        case 'in_progress': return { bg: '#e0f2fe', text: '#0284c7' };
        case 'on_hold': return { bg: '#fef3c7', text: '#b45309' };
        default: return { bg: '#f1f5f9', text: '#64748b' };
    }
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 700; color: #0f172a; margin: 0;">Projects</h1>
                <p style="color: #64748b; margin-top: 4px; font-size: 0.95rem;">Track, monitor, and deliver organizational initiatives.</p>
            </div>
            <Link v-if="can('manage projects')" href="/admin/projects/create" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                <i class="fa-solid fa-plus"></i> Create Project
            </Link>
        </div>

        <!-- Filter Bar -->
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <div style="flex: 1; min-width: 220px;">
                <input v-model="search" 
                       @keyup.enter="applyFilters"
                       type="text" 
                       placeholder="Search projects by title..." 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; outline: none;">
            </div>

            <div style="min-width: 150px;">
                <select v-model="status" @change="applyFilters" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white; outline: none;">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>

            <button @click="applyFilters" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> Filter
            </button>

            <button v-if="search || status" @click="clearFilters" style="background: transparent; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 0.85rem;">
                <i class="fa-solid fa-xmark"></i> Clear
            </button>
        </div>

        <!-- Projects Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
            <div v-for="p in projects.data" :key="p.id" style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span :style="{
                            background: statusBadgeColor(p.status).bg,
                            color: statusBadgeColor(p.status).text,
                            padding: '3px 8px',
                            borderRadius: '9999px',
                            fontSize: '0.75rem',
                            fontWeight: '700',
                            textTransform: 'capitalize'
                        }">
                            {{ p.status.replace('_', ' ') }}
                        </span>
                        <span style="font-size: 0.8rem; color: #64748b;">
                            <i class="fa-solid fa-list-check"></i> {{ p.tasks_count }} tasks
                        </span>
                    </div>

                    <h3 style="font-size: 1.2rem; font-weight: 700; margin: 0 0 0.5rem;">
                        <Link :href="`/admin/projects/${p.id}`" style="color: #0f172a; text-decoration: none;">
                            {{ p.name }}
                        </Link>
                    </h3>

                    <p style="color: #64748b; font-size: 0.875rem; line-height: 1.5; margin: 0 0 1.25rem;">
                        {{ p.description || 'No description provided.' }}
                    </p>
                </div>

                <div>
                    <!-- Progress Bar -->
                    <div style="margin-bottom: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.35rem;">
                            <span style="color: #64748b;">Completion</span>
                            <span style="font-weight: 700; color: #4f46e5;">{{ p.progress }}%</span>
                        </div>
                        <div style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 9999px; overflow: hidden;">
                            <div :style="{ width: p.progress + '%', background: '#4f46e5', height: '100%', borderRadius: '9999px' }"></div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 0.75rem;">
                        <span style="font-size: 0.8rem; color: #94a3b8;">
                            Due {{ p.end_date || 'Ongoing' }}
                        </span>
                        <div style="display: flex; gap: 6px;">
                            <Link :href="`/admin/projects/${p.id}/edit`" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: 500;">
                                Edit
                            </Link>
                            <Link :href="`/admin/projects/${p.id}`" style="background: #eef2ff; color: #4f46e5; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
                                Details &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="projects.data.length === 0" style="grid-column: 1 / -1; background: white; padding: 3rem; text-align: center; border: 1px solid #e2e8f0; border-radius: 8px; color: #94a3b8;">
                No projects found.
            </div>
        </div>

        <!-- Inertia Pagination -->
        <div v-if="projects.links && projects.links.length > 3" style="margin-top: 2rem; display: flex; justify-content: center; gap: 4px;">
            <template v-for="(link, i) in projects.links" :key="i">
                <Link v-if="link.url"
                      :href="link.url"
                      v-html="link.label"
                      :style="{
                          padding: '6px 12px',
                          borderRadius: '6px',
                          fontSize: '0.85rem',
                          textDecoration: 'none',
                          fontWeight: link.active ? '700' : '500',
                          background: link.active ? '#4f46e5' : 'white',
                          color: link.active ? 'white' : '#475569',
                          border: '1px solid #e2e8f0'
                      }" />
                <span v-else
                      v-html="link.label"
                      style="padding: 6px 12px; color: #cbd5e1; font-size: 0.85rem; border: 1px solid #f1f5f9; border-radius: 6px; background: white;" />
            </template>
        </div>
    </AdminLayout>
</template>
