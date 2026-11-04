<script setup>
import { computed, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const authUser = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash || {});

const showFlash = ref(true);

watch(() => flash.value?.success || flash.value?.error || flash.value?.info, () => {
    showFlash.value = true;
});

const isCurrentUrl = (path) => {
    return page.url.startsWith(path);
};
</script>

<template>
    <div class="admin-wrapper" style="min-height: 100vh; background-color: var(--bg-body, #f8fafc); font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <!-- Top Navbar -->
        <nav class="navbar" style="background: white; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 50; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="max-width: 1240px; margin: 0 auto; padding: 0.75rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                
                <!-- Brand & Nav Links -->
                <div style="display: flex; align-items: center; gap: 2.5rem;">
                    <Link href="/admin/dashboard" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #0f172a; font-weight: 700; font-size: 1.15rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.95rem;">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <span>TaskFlow <span style="font-size: 0.7rem; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 9999px; font-weight: 700; margin-left: 4px;">ADMIN</span></span>
                    </Link>

                    <div style="display: flex; gap: 0.5rem;">
                        <Link href="/admin/dashboard" 
                              :style="{
                                  display: 'inline-flex',
                                  alignItems: 'center',
                                  gap: '6px',
                                  padding: '6px 12px',
                                  borderRadius: '6px',
                                  textDecoration: 'none',
                                  fontSize: '0.9rem',
                                  fontWeight: '600',
                                  color: isCurrentUrl('/admin/dashboard') ? '#4f46e5' : '#64748b',
                                  background: isCurrentUrl('/admin/dashboard') ? '#eef2ff' : 'transparent'
                              }">
                            <i class="fa-solid fa-chart-pie"></i> Dashboard
                        </Link>

                        <Link v-if="can('manage projects')" 
                              href="/admin/projects" 
                              :style="{
                                  display: 'inline-flex',
                                  alignItems: 'center',
                                  gap: '6px',
                                  padding: '6px 12px',
                                  borderRadius: '6px',
                                  textDecoration: 'none',
                                  fontSize: '0.9rem',
                                  fontWeight: '600',
                                  color: isCurrentUrl('/admin/projects') ? '#4f46e5' : '#64748b',
                                  background: isCurrentUrl('/admin/projects') ? '#eef2ff' : 'transparent'
                              }">
                            <i class="fa-solid fa-folder-tree"></i> Projects
                        </Link>

                        <Link v-if="can('manage tasks')" 
                              href="/admin/tasks" 
                              :style="{
                                  display: 'inline-flex',
                                  alignItems: 'center',
                                  gap: '6px',
                                  padding: '6px 12px',
                                  borderRadius: '6px',
                                  textDecoration: 'none',
                                  fontSize: '0.9rem',
                                  fontWeight: '600',
                                  color: isCurrentUrl('/admin/tasks') ? '#4f46e5' : '#64748b',
                                  background: isCurrentUrl('/admin/tasks') ? '#eef2ff' : 'transparent'
                              }">
                            <i class="fa-solid fa-list-check"></i> Tasks
                        </Link>

                        <Link v-if="can('manage staff')" 
                              href="/admin/staff" 
                              :style="{
                                  display: 'inline-flex',
                                  alignItems: 'center',
                                  gap: '6px',
                                  padding: '6px 12px',
                                  borderRadius: '6px',
                                  textDecoration: 'none',
                                  fontSize: '0.9rem',
                                  fontWeight: '600',
                                  color: isCurrentUrl('/admin/staff') ? '#4f46e5' : '#64748b',
                                  background: isCurrentUrl('/admin/staff') ? '#eef2ff' : 'transparent'
                              }">
                            <i class="fa-solid fa-user-group"></i> Staff Members
                        </Link>
                    </div>
                </div>

                <!-- User & Logout -->
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                            {{ (authUser?.name || 'A').charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                                {{ authUser?.name || 'Administrator' }}
                                <span v-if="hasRole('super-admin')" style="background: #4f46e5; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    SUPER ADMIN
                                </span>
                                <span v-else-if="hasRole('project-admin')" style="background: #0284c7; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    PROJECT ADMIN
                                </span>
                                <span v-else-if="hasRole('auditor')" style="background: #64748b; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    AUDITOR
                                </span>
                                <span v-else style="background: #4f46e5; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    ADMIN
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b;">{{ authUser?.email }}</div>
                        </div>
                    </div>

                    <Link href="/admin/logout" 
                          method="post" 
                          as="button" 
                          style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </Link>
                </div>

            </div>
        </nav>

        <!-- Main Content Area -->
        <main style="max-width: 1240px; margin: 0 auto; padding: 2rem 1.5rem;">
            <!-- Flash Messages -->
            <div v-if="flash.success && showFlash" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                    <span>{{ flash.success }}</span>
                </div>
                <button @click="showFlash = false" style="background: transparent; border: none; color: #065f46; cursor: pointer; font-size: 1rem;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div v-if="flash.error && showFlash" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 0.9rem;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
                    <span>{{ flash.error }}</span>
                </div>
                <button @click="showFlash = false" style="background: transparent; border: none; color: #991b1b; cursor: pointer; font-size: 1rem;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Page Component Slot -->
            <slot />
        </main>
    </div>
</template>
