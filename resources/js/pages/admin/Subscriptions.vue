<script setup lang="ts">
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import SubscriptionCapturePreview from '@/components/admin/subscriptions/SubscriptionCapturePreview.vue';
import SubscriptionFormModal from '@/components/admin/subscriptions/SubscriptionFormModal.vue';
import SubscriptionStats from '@/components/admin/subscriptions/SubscriptionStats.vue';
import SubscriptionsTable from '@/components/admin/subscriptions/SubscriptionsTable.vue';
import { useSubscriptionForm } from '@/composables/admin/useSubscriptionForm';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import type { PaginatedResponse } from '@/types/pagination';
import type { Subscription } from '@/types/subscription';
import { Head, router, usePage } from '@inertiajs/vue3';
import { AlertCircle, Check, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    subscriptions: PaginatedResponse<Subscription>;
    filters: { search?: string };
    planOptions?: { slug: string; name: string; price: number; months: number }[];
}>();

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});

const formCtx = useSubscriptionForm(props.planOptions ?? []);
const selectedSub = ref<Subscription | null>(null);
const searchQuery = ref(props.filters.search || '');
const filtered = ref<Subscription[]>(props.subscriptions.data);

const activeCount = computed(() => props.subscriptions.data.filter((s) => s.status === 'activa').length);
const paginationLinks = usePaginationLinks(props.subscriptions.links);

watch(
    searchQuery,
    () => {
        router.get(route('admin.subscriptions.index'), { search: searchQuery.value || undefined }, { preserveState: false, replace: true });
    },
    { immediate: false },
);

watch(
    () => props.subscriptions.data,
    (d) => {
        filtered.value = d;
    },
);

function handleSubmit() {
    formCtx.submit(() => {
        filtered.value = props.subscriptions.data;
    });
}
</script>

<template>
    <Head title="Suscripciones Premium - Admin IEE" />
    <AppLayout>
        <div class="mx-auto max-w-7xl space-y-10 px-4 py-8">
            <AdminPageHeader
                title="Membresías"
                subtitle="Suscripciones Premium activas y asignación manual."
                action-label="Nueva suscripción"
                action-order="first"
                compact
                @action="formCtx.open"
            />

            <SubscriptionStats :active-count="activeCount" :total-count="subscriptions.total" />

            <div class="flex flex-col items-center gap-3 rounded-[2.5rem] border border-slate-100 bg-slate-50 p-3 lg:flex-row">
                <div class="relative w-full flex-1 lg:w-auto">
                    <Search class="absolute left-5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Filtrar por nombre de estudiante o correo..."
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-6 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                    />
                </div>
            </div>

            <SubscriptionsTable :subscriptions="filtered" @preview="selectedSub = $event" />
            <AdminPagination :links="paginationLinks" label="Navegación de Suscripciones" />
        </div>

        <SubscriptionFormModal
            :show="formCtx.showModal.value"
            :form="formCtx.form"
            :plan-options="planOptions ?? []"
            :users-results="formCtx.usersResults.value"
            :search-user-query="formCtx.searchUserQuery.value"
            :is-searching-user="formCtx.isSearchingUser.value"
            @close="formCtx.showModal.value = false"
            @submit="handleSubmit"
            @update:search-user-query="formCtx.searchUserQuery.value = $event"
            @search="formCtx.searchUsers"
            @select-user="formCtx.selectUser"
            @file-change="formCtx.onFileChange"
        />

        <SubscriptionCapturePreview :subscription="selectedSub" @close="selectedSub = null" />

        <Transition
            enter-active-class="transition duration-500"
            enter-from-class="translate-y-full opacity-0"
            enter-to-class="translate-y-0 opacity-100"
        >
            <div
                v-if="flash.success || flash.error"
                class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl bg-slate-900 p-2 pr-6 text-white shadow-2xl"
            >
                <div :class="`flex h-10 w-10 items-center justify-center rounded-full ${flash.success ? 'bg-emerald-500' : 'bg-rose-500'}`">
                    <Check v-if="flash.success" class="h-6 w-6 text-white" />
                    <AlertCircle v-else class="h-6 w-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <span :class="`text-[10px] font-bold uppercase tracking-widest ${flash.success ? 'text-emerald-500' : 'text-rose-500'}`">{{
                        flash.success ? 'Sistema Sincronizado' : 'Error Técnico'
                    }}</span>
                    <span class="text-sm font-medium">{{ flash.success || flash.error }}</span>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>
