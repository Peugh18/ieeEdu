<script setup lang="ts">
import { Calendar, Filter, Search } from 'lucide-vue-next';
import AppSelect from '@/components/ui/AppSelect.vue';

const search = defineModel<string>('search', { default: '' });
const status = defineModel<string>('status', { default: '' });
const date = defineModel<string>('date', { default: '' });

const emit = defineEmits<{
    (e: 'filter'): void;
}>();
</script>

<template>
    <!-- Filter Bar -->
    <div class="flex flex-col items-center gap-4 rounded-[2.5rem] border border-slate-100 bg-slate-50 p-4 lg:flex-row">
        <div class="relative w-full flex-1 lg:w-auto">
            <Search class="absolute left-5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input
                v-model="search"
                placeholder="Buscar por estudiante, curso o ID..."
                class="h-14 w-full rounded-2xl border border-slate-200 bg-white pl-12 pr-6 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                @keydown.enter.prevent="emit('filter')"
            />
        </div>
        <div class="flex w-full flex-wrap items-center gap-3 lg:w-auto">
            <div class="relative min-w-[160px] flex-1 lg:flex-none">
                <Filter class="absolute left-4 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                <AppSelect
                    v-model="status"
                    :options="[
                        { value: '', label: 'Todos los Estados' },
                        { value: 'pendiente', label: 'Pendiente' },
                        { value: 'en_revision', label: 'En revisión' },
                        { value: 'aprobado', label: 'Aprobado' },
                        { value: 'rechazado', label: 'Rechazado' }
                    ]"
                    class="h-14 border-slate-200 bg-white font-bold pl-10 shadow-none text-xs"
                />
            </div>
            <div class="relative min-w-[160px] flex-1 lg:flex-none">
                <Calendar class="absolute left-4 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="date"
                    type="date"
                    class="h-14 w-full appearance-none rounded-2xl border border-slate-200 bg-white pl-10 pr-4 text-xs font-bold text-slate-600 outline-none"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
