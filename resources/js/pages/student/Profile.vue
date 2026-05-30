<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { User, BookOpen, Crown, Settings as SettingsIcon } from 'lucide-vue-next';
import type { SharedData } from '@/types';
import ProfileSidebar from '@/components/student/profile/ProfileSidebar.vue';
import ProfileCoursesTab from '@/components/student/profile/ProfileCoursesTab.vue';
import ProfileSubscriptionTab from '@/components/student/profile/ProfileSubscriptionTab.vue';
import ProfileConfigTab from '@/components/student/profile/ProfileConfigTab.vue';
import BottomNav from '@/components/student/BottomNav.vue';

interface Props {
    enrolledCourses: { id: number; title: string; slug: string; image: string; progress: number; last_lesson?: string }[];
    activeSubscription?: { type: string; end_date: string } | null;
}

const props = defineProps<Props>();

const page = usePage<SharedData>();
const authUser = computed(() => page.props.auth?.user);

const activeTab = ref('perfil');
const fileInputRef = ref<HTMLInputElement | null>(null);
const avatarPreview = ref<string | null>(null);

const profileForm = useForm({ name: authUser.value?.name ?? '', avatar: null as File | null });
const passwordForm = useForm({ password: '', password_confirmation: '' });

function handleAvatarChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) { profileForm.avatar = file; const reader = new FileReader(); reader.onload = (ev) => { avatarPreview.value = ev.target?.result as string; }; reader.readAsDataURL(file); }
}

function updateProfileData() {
    profileForm.post(route('student.profile.custom.update'), { preserveScroll: true, forceFormData: true });
}

function updatePasswordData() {
    passwordForm.post(route('student.profile.custom.update'), { preserveScroll: true, onSuccess: () => passwordForm.reset() });
}

const tabs = [
    { id: 'perfil', label: 'Mi Perfil', icon: User },
    { id: 'cursos', label: 'Mis Cursos', icon: BookOpen },
    { id: 'premium', label: 'Suscripción', icon: Crown },
    { id: 'config', label: 'Configuración', icon: SettingsIcon },
];
</script>

<template>
    <Head title="Perfil de Usuario - IEE" />
    <AppLayout>
        <div class="p-6 md:p-12 max-w-[1400px] mx-auto min-h-[85vh]">
            <input type="file" ref="fileInputRef" class="hidden" accept="image/*" @change="handleAvatarChange" />
            <div class="flex flex-col lg:flex-row gap-8 mt-4 relative items-start">
                <ProfileSidebar :tabs="tabs" :active-tab="activeTab" :user="{ name: authUser?.name ?? '', email: authUser?.email ?? '', avatar: authUser?.avatar }" :has-subscription="!!props.activeSubscription" @update:active-tab="activeTab = $event" @avatar-click="fileInputRef?.click()" />
                <main class="flex-1 w-full min-w-0">
                    <div class="bg-white rounded-3xl border border-outline-variant/30 shadow-sm p-8 lg:p-12 min-h-[500px]">
                        <section v-if="activeTab === 'perfil'" class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div><h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Información Académica</h2><p class="text-on-surface-variant text-sm">Resumen de tu formación en el instituto.</p></div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-surface p-6 rounded-2xl border border-outline-variant/20 flex gap-4 items-center"><div class="p-4 bg-primary/10 text-primary rounded-2xl shrink-0"><User class="w-6 h-6"/></div><div><p class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">Cursos Activos</p><p class="font-black text-2xl text-on-surface mt-1">{{ props.enrolledCourses.filter((c) => c.progress < 100).length }}</p></div></div>
                                <div class="bg-surface p-6 rounded-2xl border border-outline-variant/20 flex gap-4 items-center"><div class="p-4 bg-green-100 text-green-700 rounded-2xl shrink-0"><BookOpen class="w-6 h-6"/></div><div><p class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">Cursos Completados</p><p class="font-black text-2xl text-on-surface mt-1">{{ props.enrolledCourses.filter((c) => c.progress === 100).length }}</p></div></div>
                            </div>
                            <div class="mt-8 pt-8 lg:mt-0 lg:pt-0">
                                <h3 class="text-xl font-serif font-bold text-on-surface mb-6">Detalles de la Cuenta</h3>
                                <form @submit.prevent="updateProfileData" class="space-y-6 max-w-xl bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-8">
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Nombre Completo</label>
                                        <input v-model="profileForm.name" type="text" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" />
                                        <p v-if="profileForm.errors.name" class="text-red-500 text-xs mt-1">{{ profileForm.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Correo Electrónico</label>
                                        <input :value="authUser?.email" type="email" disabled class="w-full bg-surface-container border border-outline-variant/20 rounded-xl px-4 py-3 text-sm text-on-surface-variant opacity-70 cursor-not-allowed" />
                                        <p class="text-xs text-on-surface-variant/70">El email no puede ser modificado aquí.</p>
                                    </div>
                                    <div class="pt-4 flex items-center gap-4">
                                        <button type="submit" :disabled="profileForm.processing" class="inline-flex bg-primary text-white font-bold text-xs uppercase tracking-widest px-8 py-3 rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50">{{ profileForm.processing ? 'Guardando...' : 'Guardar Cambios' }}</button>
                                        <span v-if="profileForm.wasSuccessful" class="text-green-600 text-xs font-bold">✓ Guardado</span>
                                    </div>
                                </form>
                            </div>
                        </section>
                        <ProfileCoursesTab v-else-if="activeTab === 'cursos'" :courses="props.enrolledCourses" />
                        <ProfileSubscriptionTab v-else-if="activeTab === 'premium'" :subscription="props.activeSubscription" />
                        <ProfileConfigTab v-else-if="activeTab === 'config'" :password-form="{ password: passwordForm.password, password_confirmation: passwordForm.password_confirmation, processing: passwordForm.processing, errors: passwordForm.errors as Record<string, string> }" @update:password="(field: 'password' | 'password_confirmation', val: string) => { (passwordForm as unknown as Record<string, string>)[field] = val; }" @submit="updatePasswordData" />
                    </div>
                </main>
            </div>
        </div>
        <BottomNav />
    </AppLayout>
</template>
