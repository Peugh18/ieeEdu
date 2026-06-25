<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import ProfileConfigTab from '@/components/student/profile/ProfileConfigTab.vue';
import ProfileCoursesTab from '@/components/student/profile/ProfileCoursesTab.vue';
import ProfileSidebar from '@/components/student/profile/ProfileSidebar.vue';
import ProfileSubscriptionTab from '@/components/student/profile/ProfileSubscriptionTab.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { BookOpen, Crown, Settings as SettingsIcon, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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
    if (file) {
        profileForm.avatar = file;
        const reader = new FileReader();
        reader.onload = (ev) => {
            avatarPreview.value = ev.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
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
        <div class="mx-auto min-h-[85vh] max-w-[1400px] p-6 md:p-12">
            <input type="file" ref="fileInputRef" class="hidden" accept="image/*" @change="handleAvatarChange" />
            <div class="relative mt-4 flex flex-col items-start gap-8 lg:flex-row">
                <ProfileSidebar
                    :tabs="tabs"
                    :active-tab="activeTab"
                    :user="{ name: authUser?.name ?? '', email: authUser?.email ?? '', avatar: authUser?.avatar }"
                    :has-subscription="!!props.activeSubscription"
                    @update:active-tab="activeTab = $event"
                    @avatar-click="fileInputRef?.click()"
                />
                <main class="w-full min-w-0 flex-1">
                    <div class="min-h-[500px] rounded-3xl border border-outline-variant/30 bg-white p-8 shadow-sm lg:p-12">
                        <section v-if="activeTab === 'perfil'" class="space-y-10 duration-500 animate-in fade-in slide-in-from-bottom-4">
                            <div>
                                <h2 class="mb-2 font-serif text-3xl font-bold text-on-surface">Información Académica</h2>
                                <p class="text-sm text-on-surface-variant">Resumen de tu formación en el instituto.</p>
                            </div>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="flex items-center gap-4 rounded-2xl border border-outline-variant/20 bg-surface p-6">
                                    <div class="shrink-0 rounded-2xl bg-primary/10 p-4 text-primary"><User class="h-6 w-6" /></div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Cursos Activos</p>
                                        <p class="mt-1 text-2xl font-black text-on-surface">
                                            {{ props.enrolledCourses.filter((c) => c.progress < 100).length }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 rounded-2xl border border-outline-variant/20 bg-surface p-6">
                                    <div class="shrink-0 rounded-2xl bg-green-100 p-4 text-green-700"><BookOpen class="h-6 w-6" /></div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Cursos Completados</p>
                                        <p class="mt-1 text-2xl font-black text-on-surface">
                                            {{ props.enrolledCourses.filter((c) => c.progress === 100).length }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 pt-8 lg:mt-0 lg:pt-0">
                                <h3 class="mb-6 font-serif text-xl font-bold text-on-surface">Detalles de la Cuenta</h3>
                                <form
                                    @submit.prevent="updateProfileData"
                                    class="max-w-xl space-y-6 rounded-3xl border border-outline-variant/30 bg-surface-container-lowest p-8"
                                >
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold uppercase tracking-widest text-on-surface-variant">Nombre Completo</label>
                                        <input
                                            v-model="profileForm.name"
                                            type="text"
                                            class="w-full rounded-xl border border-outline-variant/30 bg-surface px-4 py-3 text-sm transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary"
                                        />
                                        <p v-if="profileForm.errors.name" class="mt-1 text-xs text-red-500">{{ profileForm.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold uppercase tracking-widest text-on-surface-variant">Correo Electrónico</label>
                                        <input
                                            :value="authUser?.email"
                                            type="email"
                                            disabled
                                            class="w-full cursor-not-allowed rounded-xl border border-outline-variant/20 bg-surface-container px-4 py-3 text-sm text-on-surface-variant opacity-70"
                                        />
                                        <p class="text-xs text-on-surface-variant/70">El email no puede ser modificado aquí.</p>
                                    </div>
                                    <div class="flex items-center gap-4 pt-4">
                                        <button
                                            type="submit"
                                            :disabled="profileForm.processing"
                                            class="inline-flex rounded-xl bg-primary px-8 py-3 text-xs font-bold uppercase tracking-widest text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                                        >
                                            {{ profileForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                        </button>
                                        <span v-if="profileForm.wasSuccessful" class="text-xs font-bold text-green-600">✓ Guardado</span>
                                    </div>
                                </form>
                            </div>
                        </section>
                        <ProfileCoursesTab v-else-if="activeTab === 'cursos'" :courses="props.enrolledCourses" />
                        <ProfileSubscriptionTab v-else-if="activeTab === 'premium'" :subscription="props.activeSubscription" />
                        <ProfileConfigTab
                            v-else-if="activeTab === 'config'"
                            :password-form="{
                                password: passwordForm.password,
                                password_confirmation: passwordForm.password_confirmation,
                                processing: passwordForm.processing,
                                errors: passwordForm.errors as Record<string, string>,
                            }"
                            @update:password="
                                (field: 'password' | 'password_confirmation', val: string) => {
                                    (passwordForm as unknown as Record<string, string>)[field] = val;
                                }
                            "
                            @submit="updatePasswordData"
                        />
                    </div>
                </main>
            </div>
        </div>
        <BottomNav />
    </AppLayout>
</template>
