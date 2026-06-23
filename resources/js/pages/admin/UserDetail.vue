<script setup lang="ts">
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import UserEnrollmentsTab from '@/components/admin/users/UserEnrollmentsTab.vue';
import UserPaymentsTab from '@/components/admin/users/UserPaymentsTab.vue';
import UserProfileCard from '@/components/admin/users/UserProfileCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import type { UserDetail } from '@/types/user-detail';
import { Head, router, usePage } from '@inertiajs/vue3';
import { BookOpen, Check, Wallet } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    user: UserDetail;
    enrolledIds: number[];
}>();

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});

const activeTab = ref<'cursos' | 'pagos'>('cursos');

function toggleStatus() {
    router.patch(route('admin.users.toggleStatus', { user: props.user.id }), {}, { preserveScroll: true });
}

const tabs = [
    { id: 'cursos' as const, label: 'Historial Académico', icon: BookOpen },
    { id: 'pagos' as const, label: 'Gestión Financiera', icon: Wallet },
];
</script>

<template>
    <Head :title="`Ficha: ${user.name} - iieEdu Admin`" />
    <AppLayout>
        <div class="mx-auto min-h-screen max-w-7xl space-y-8 px-4 py-8">
            <AdminPageHeader
                title="Perfil de"
                title-accent="Usuario"
                :subtitle="`Gestión de cuenta y progreso de ${user.name}`"
                back-link="admin.users.index"
                back-label="Volver al directorio"
                compact
            >
                <template #actions>
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                        <span class="text-on-surface-variant/50">Admin</span>
                        <span class="text-on-surface-variant/50">/</span>
                        <span class="text-on-surface-variant/50">Usuarios</span>
                        <span class="text-on-surface-variant/50">/</span>
                        <span class="text-primary">Perfil</span>
                    </div>
                </template>
            </AdminPageHeader>

            <UserProfileCard :user="user" @toggle-status="toggleStatus" />

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <div class="hidden flex-col gap-2 lg:col-span-3 lg:flex">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="flex items-center justify-between rounded-2xl border px-6 py-4 transition-all duration-300"
                        :class="
                            activeTab === tab.id
                                ? 'border-primary bg-primary text-white shadow-xl'
                                : 'border-transparent bg-white text-on-surface-variant hover:bg-surface-container-low'
                        "
                    >
                        <div class="flex items-center gap-3">
                            <component :is="tab.icon" class="h-5 w-5" :class="activeTab === tab.id ? 'text-white' : 'text-on-surface-variant/50'" />
                            <span class="text-sm font-bold tracking-tight">{{ tab.label }}</span>
                        </div>
                    </button>
                </div>

                <div class="lg:col-span-9">
                    <UserEnrollmentsTab v-if="activeTab === 'cursos'" :enrollments="user.enrollments" />
                    <UserPaymentsTab v-else :payments="user.payments" />
                </div>
            </div>
        </div>

        <Transition
            enter-active-class="transition duration-500"
            enter-from-class="translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
        >
            <div
                v-if="flash.success"
                class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl border border-outline-variant/10 bg-surface-container-highest p-2 pr-6 text-on-surface shadow-2xl"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500"><Check class="h-6 w-6 text-white" /></div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Operación Exitosa</span>
                    <span class="text-sm font-medium">{{ flash.success }}</span>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>
