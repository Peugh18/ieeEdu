<script setup lang="ts">
import type { ConsultancyRequest } from '@/types/consultancy-request';
import { Briefcase, Mail, Phone, Search } from 'lucide-vue-next';
import AppSelect from '@/components/ui/AppSelect.vue';

defineProps<{
    requests: ConsultancyRequest[];
    viewMode: 'cards' | 'list';
}>();

const emit = defineEmits<{
    (e: 'updateStatus', id: number, status: string): void;
}>();

function getStatusClasses(status: string) {
    if (status === 'pendiente') return 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400';
    if (status === 'en_contacto') return 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400';
    if (status === 'cerrado') return 'bg-green-100 text-green-700 border-green-200 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400';
    return 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400';
}

function statusBtnClass(current: string, target: string) {
    if (current === target) {
        if (target === 'pendiente') return 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400';
        if (target === 'en_contacto') return 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-500/10 dark:border-blue-500/20 dark:text-blue-400';
        return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400';
    }
    return 'bg-transparent text-on-surface-variant border-outline-variant/20 hover:bg-surface-container-high dark:hover:bg-surface-2';
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

    <!-- Vista de tarjetas compactas -->
    <div v-else-if="viewMode === 'cards'" class="flex flex-col gap-2">
        <div
            v-for="req in requests"
            :key="req.id"
            class="group relative flex flex-col items-start gap-4 overflow-hidden rounded-2xl border border-outline-variant/10 bg-surface-container-lowest px-5 py-4 transition-all hover:border-outline-variant/30 hover:shadow-sm dark:bg-surface-1 sm:flex-row sm:items-center"
        >
            <!-- barra de color lateral -->
            <div
                class="absolute bottom-0 left-0 top-0 w-1"
                :class="{
                    'bg-amber-400': req.status === 'pendiente',
                    'bg-blue-400': req.status === 'en_contacto',
                    'bg-green-400': req.status === 'cerrado',
                }"
            ></div>

            <!-- Badge + fecha -->
            <div class="ml-3 flex shrink-0 flex-col items-start gap-1">
                <span
                    class="rounded-full border px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-widest"
                    :class="getStatusClasses(req.status)"
                >{{ req.status }}</span>
                <span class="text-[10px] text-on-surface-variant/60">{{ new Date(req.created_at).toLocaleDateString() }}</span>
            </div>

            <!-- Nombre + área -->
            <div class="ml-3 min-w-0 flex-1 sm:ml-0">
                <p class="text-sm font-bold text-on-surface">{{ req.name }}</p>
                <p class="max-w-xs truncate text-xs font-medium text-primary" :title="req.area">{{ req.area }}</p>
            </div>

            <!-- Contacto -->
            <div class="hidden min-w-0 flex-shrink-0 flex-col gap-0.5 lg:flex">
                <a :href="'mailto:' + req.email" class="flex items-center gap-1.5 text-xs text-on-surface-variant hover:text-primary">
                    <Mail class="h-3 w-3 shrink-0" /> <span class="truncate max-w-[180px]">{{ req.email }}</span>
                </a>
                <a v-if="req.phone" :href="'tel:' + req.phone" class="flex items-center gap-1.5 text-xs text-on-surface-variant hover:text-primary">
                    <Phone class="h-3 w-3 shrink-0" /> {{ req.phone }}
                </a>
            </div>

            <!-- Mensaje truncado -->
            <p class="hidden max-w-[220px] truncate text-xs text-on-surface-variant/60 xl:block" :title="req.message">
                {{ req.message }}
            </p>

            <!-- Acciones de estado -->
            <div class="ml-3 flex shrink-0 gap-2 sm:ml-0">
                <button
                    @click="emit('updateStatus', req.id, 'pendiente')"
                    class="rounded-lg border px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide transition-colors"
                    :class="statusBtnClass(req.status, 'pendiente')"
                >Pendiente</button>
                <button
                    @click="emit('updateStatus', req.id, 'en_contacto')"
                    class="rounded-lg border px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide transition-colors"
                    :class="statusBtnClass(req.status, 'en_contacto')"
                >Contactado</button>
                <button
                    @click="emit('updateStatus', req.id, 'cerrado')"
                    class="rounded-lg border px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide transition-colors"
                    :class="statusBtnClass(req.status, 'cerrado')"
                >Resuelto</button>
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
                            <AppSelect
                                :modelValue="req.status"
                                @update:modelValue="(val) => emit('updateStatus', req.id, String(val))"
                                :options="[
                                    { value: 'pendiente', label: 'Pendiente' },
                                    { value: 'en_contacto', label: 'Contactado' },
                                    { value: 'cerrado', label: 'Resuelto' }
                                ]"
                                class="bg-surface-container font-bold border-0 text-xs shadow-none"
                            />
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
