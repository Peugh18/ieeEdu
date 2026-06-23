<script setup lang="ts">
import type { ConsultancyRequest } from '@/types/consultancy-request';
import { Briefcase, Mail, Phone, Search } from 'lucide-vue-next';

defineProps<{
    requests: ConsultancyRequest[];
    viewMode: 'cards' | 'list';
}>();

const emit = defineEmits<{
    (e: 'updateStatus', id: number, status: string): void;
}>();

function getStatusClasses(status: string) {
    if (status === 'pendiente') return 'bg-amber-100 text-amber-700 border-amber-200';
    if (status === 'en_contacto') return 'bg-blue-100 text-blue-700 border-blue-200';
    if (status === 'cerrado') return 'bg-green-100 text-green-700 border-green-200';
    return 'bg-gray-100 text-gray-700 border-gray-200';
}

function statusBtnClass(current: string, target: string) {
    if (current === target) {
        if (target === 'pendiente') return 'bg-amber-100 text-amber-800 border-amber-200';
        if (target === 'en_contacto') return 'bg-blue-100 text-blue-800 border-blue-200';
        return 'bg-green-100 text-green-800 border-green-200';
    }
    return 'bg-transparent text-on-surface-variant border-outline-variant/20 hover:bg-surface-container-high';
}
</script>

<template>
    <div v-if="!requests.length" class="rounded-[2rem] border border-outline-variant/10 bg-surface-container-lowest p-16 text-center">
        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-primary/5">
            <Search class="h-10 w-10 text-primary/40" />
        </div>
        <h3 class="mb-2 text-xl font-bold text-on-surface">No se encontraron solicitudes</h3>
        <p class="mx-auto max-w-md text-sm text-on-surface-variant">No hay registros que coincidan con tu búsqueda o filtros actuales.</p>
    </div>

    <div v-else-if="viewMode === 'cards'" class="grid grid-cols-1 gap-6">
        <div
            v-for="req in requests"
            :key="req.id"
            class="group relative overflow-hidden rounded-[2rem] border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm transition-shadow hover:shadow-md"
        >
            <div
                class="absolute bottom-0 left-0 top-0 w-1.5"
                :class="{
                    'bg-amber-400': req.status === 'pendiente',
                    'bg-blue-400': req.status === 'en_contacto',
                    'bg-green-400': req.status === 'cerrado',
                }"
            ></div>
            <div class="flex flex-col items-start gap-8 pl-4 lg:flex-row lg:items-center">
                <div class="w-full flex-1">
                    <div class="mb-3 flex items-center gap-3">
                        <span
                            class="rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-widest"
                            :class="getStatusClasses(req.status)"
                            >{{ req.status }}</span
                        >
                        <span class="text-xs font-medium text-on-surface-variant">Hace {{ new Date(req.created_at).toLocaleDateString() }}</span>
                    </div>
                    <h3 class="mb-1 text-xl font-bold text-on-surface">{{ req.name }}</h3>
                    <p class="mb-4 text-sm font-medium text-primary">{{ req.area }}</p>
                    <div class="mb-4 flex flex-wrap gap-x-6 gap-y-2">
                        <a
                            :href="'mailto:' + req.email"
                            class="flex items-center gap-2 text-sm text-on-surface-variant transition-colors hover:text-primary"
                            ><Mail class="h-4 w-4" /> {{ req.email }}</a
                        >
                        <a
                            v-if="req.phone"
                            :href="'tel:' + req.phone"
                            class="flex items-center gap-2 text-sm text-on-surface-variant transition-colors hover:text-primary"
                            ><Phone class="h-4 w-4" /> {{ req.phone }}</a
                        >
                        <span v-if="req.company" class="flex items-center gap-2 text-sm text-on-surface-variant"
                            ><Briefcase class="h-4 w-4" /> {{ req.company }}</span
                        >
                    </div>
                    <div class="rounded-2xl border border-outline-variant/10 bg-surface-container p-4 text-sm leading-relaxed text-on-surface">
                        <span class="mb-2 block text-xs font-bold uppercase tracking-widest text-on-surface-variant">Mensaje:</span>
                        {{ req.message }}
                    </div>
                </div>
                <div
                    class="flex w-full flex-row gap-3 border-t border-outline-variant/10 pt-6 lg:w-48 lg:flex-col lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0"
                >
                    <p class="mb-2 hidden w-full text-[10px] font-bold uppercase tracking-widest text-on-surface-variant lg:block">Cambiar estado:</p>
                    <button
                        @click="emit('updateStatus', req.id, 'pendiente')"
                        class="flex-1 rounded-xl border px-4 py-2.5 text-xs font-bold transition-colors lg:w-full"
                        :class="statusBtnClass(req.status, 'pendiente')"
                    >
                        Pendiente
                    </button>
                    <button
                        @click="emit('updateStatus', req.id, 'en_contacto')"
                        class="flex-1 rounded-xl border px-4 py-2.5 text-xs font-bold transition-colors lg:w-full"
                        :class="statusBtnClass(req.status, 'en_contacto')"
                    >
                        Contactado
                    </button>
                    <button
                        @click="emit('updateStatus', req.id, 'cerrado')"
                        class="flex-1 rounded-xl border px-4 py-2.5 text-xs font-bold transition-colors lg:w-full"
                        :class="statusBtnClass(req.status, 'cerrado')"
                    >
                        Resuelto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="overflow-hidden rounded-[2rem] border border-outline-variant/10 bg-surface-container-lowest shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap text-left text-sm">
                <thead class="bg-surface-container-low text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                    <tr>
                        <th class="px-6 py-4">Estado / Fecha</th>
                        <th class="px-6 py-4">Prospecto</th>
                        <th class="px-6 py-4">Contacto</th>
                        <th class="min-w-[250px] px-6 py-4">Mensaje</th>
                        <th class="px-6 py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <tr v-for="req in requests" :key="req.id" class="transition-colors hover:bg-surface-container-lowest/50">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5">
                                <span
                                    class="inline-block w-max rounded-lg border px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest"
                                    :class="getStatusClasses(req.status)"
                                    >{{ req.status }}</span
                                >
                                <span class="text-xs text-on-surface-variant">{{ new Date(req.created_at).toLocaleDateString() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-on-surface">{{ req.name }}</p>
                            <p class="max-w-[200px] truncate text-xs font-medium text-primary" :title="req.area">{{ req.area }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="mb-1 text-xs text-on-surface">
                                <a :href="'mailto:' + req.email" class="hover:underline">{{ req.email }}</a>
                            </p>
                            <p class="flex items-center gap-2 text-xs text-on-surface-variant">
                                <span v-if="req.phone"><Phone class="inline h-3 w-3" /> {{ req.phone }}</span>
                                <span v-if="req.company"><Briefcase class="inline h-3 w-3" /> {{ req.company }}</span>
                            </p>
                        </td>
                        <td class="min-w-[250px] whitespace-normal px-6 py-4">
                            <p class="line-clamp-2 text-xs text-on-surface-variant" :title="req.message">{{ req.message }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <select
                                :value="req.status"
                                @change="(e) => emit('updateStatus', req.id, (e.target as HTMLSelectElement).value)"
                                class="cursor-pointer rounded-xl border-none bg-surface-container px-3 py-2 text-xs font-bold outline-none focus:ring-1 focus:ring-primary/30"
                            >
                                <option value="pendiente">Pendiente</option>
                                <option value="en_contacto">Contactado</option>
                                <option value="cerrado">Resuelto</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
}
</style>
