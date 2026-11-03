<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    rememberedEmail: String,
    rememberedPassword: String,
});

const page = usePage();
const flash = computed(() => page.props.flash || {});

const form = useForm({
    email: props.rememberedEmail || '',
    password: props.rememberedPassword || '',
    remember: !!props.rememberedEmail,
});

const submit = () => {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div style="min-height: 100vh; background-color: var(--bg-body, #f8fafc); font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <!-- Navbar -->
        <nav style="background: white; border-bottom: 1px solid #e2e8f0; padding: 0.85rem 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
                <Link href="/" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #0f172a; font-weight: 700; font-size: 1.25rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.25);">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span>TaskFlow</span>
                </Link>

                <Link href="/staff/login" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.55rem 1.15rem; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25); transition: opacity 0.2s ease;">
                    <i class="fa-solid fa-user-check"></i> Staff Login
                </Link>
            </div>
        </nav>

        <!-- Login Card -->
        <div style="max-width: 440px; margin: 3.5rem auto; padding: 0 1.5rem;">
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <!-- Header Icon -->
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #ffffff; margin-bottom: 1rem; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.025em; color: #0f172a; margin: 0;">Admin Portal</h2>
                    <p style="color: #64748b; font-size: 0.9rem; margin-top: 0.25rem;">Sign in to your administrator account</p>
                </div>

                <!-- Flash info/error -->
                <div v-if="flash.info" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.65rem 0.85rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-info"></i> {{ flash.info }}
                </div>

                <form @submit.prevent="submit">
                    <!-- Email -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Email Address
                        </label>
                        <input v-model="form.email" 
                               type="email" 
                               required 
                               autofocus
                               placeholder="admin@example.com"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;">
                        <div v-if="form.errors.email" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Password -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-weight: 600; font-size: 0.875rem; color: #334155; margin-bottom: 6px;">
                            Password
                        </label>
                        <input v-model="form.password" 
                               type="password" 
                               required 
                               placeholder="••••••••"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; box-sizing: border-box; outline: none;">
                        <div v-if="form.errors.password" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px;">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #64748b; cursor: pointer; user-select: none;">
                            <input v-model="form.remember" type="checkbox" style="cursor: pointer;">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="form.processing"
                            style="width: 100%; padding: 10px; font-size: 0.95rem; font-weight: 600; color: white; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);">
                        <i class="fa-solid fa-right-to-bracket"></i> {{ form.processing ? 'Signing in...' : 'Sign In' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
