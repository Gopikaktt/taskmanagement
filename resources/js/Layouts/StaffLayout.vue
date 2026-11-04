<script setup>
import { computed, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const authUser = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash || {});

const showFlash = ref(true);

// Reset flash banner visibility when flash message changes
watch(() => flash.value?.success || flash.value?.error, () => {
    showFlash.value = true;
});

const isCurrentUrl = (path) => {
    return page.url.startsWith(path);
};
</script>

<template>
    <div class="layout-wrapper" style="min-height: 100vh; background-color: var(--bg-body, #f8fafc); font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <!-- Top Navbar -->
        <nav class="navbar" style="background: white; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 50; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="max-width: 1240px; margin: 0 auto; padding: 0.75rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                
                <!-- Brand & Nav Links -->
                <div style="display: flex; align-items: center; gap: 2.5rem;">
                    <Link href="/staff/dashboard" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #0f172a; font-weight: 700; font-size: 1.15rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.95rem;">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <span>TaskFlow <span style="font-size: 0.7rem; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 9999px; font-weight: 700; margin-left: 4px;">STAFF</span></span>
                    </Link>

                    <div style="display: flex; gap: 0.5rem;">
                        <Link href="/staff/dashboard" 
                              :style="{
                                  display: 'inline-flex',
                                  alignItems: 'center',
                                  gap: '6px',
                                  padding: '6px 12px',
                                  borderRadius: '6px',
                                  textDecoration: 'none',
                                  fontSize: '0.9rem',
                                  fontWeight: '600',
                                  color: isCurrentUrl('/staff/dashboard') ? '#059669' : '#64748b',
                                  background: isCurrentUrl('/staff/dashboard') ? '#ecfdf5' : 'transparent'
                              }">
                            <i class="fa-solid fa-gauge"></i> Overview
                        </Link>
                        <Link href="/staff/tasks" 
                              :style="{
                                  display: 'inline-flex',
                                  alignItems: 'center',
                                  gap: '6px',
                                  padding: '6px 12px',
                                  borderRadius: '6px',
                                  textDecoration: 'none',
                                  fontSize: '0.9rem',
                                  fontWeight: '600',
                                  color: isCurrentUrl('/staff/tasks') ? '#059669' : '#64748b',
                                  background: isCurrentUrl('/staff/tasks') ? '#ecfdf5' : 'transparent'
                              }">
                            <i class="fa-solid fa-list-check"></i> My Tasks
                        </Link>
                    </div>
                </div>

                <!-- User & Logout -->
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); color: white; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                            {{ (authUser?.name || 'S').charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                                {{ authUser?.name }}
                                <!-- Spatie Role Badge in Layout -->
                                <span v-if="hasRole('manager')" style="background: #7c3aed; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    ★ MANAGER
                                </span>
                                <span v-else-if="hasRole('team-lead')" style="background: #2563eb; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    ★ TEAM LEAD
                                </span>
                                <span v-else-if="hasRole('intern')" style="background: #eab308; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    ★ INTERN
                                </span>
                                <span v-else style="background: #94a3b8; color: white; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                    ★ MEMBER
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b;">{{ authUser?.email }}</div>
                        </div>
                    </div>

                    <Link href="/staff/logout" 
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
            <!-- Flash Success / Error Messages -->
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
