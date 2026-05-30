<script setup lang="ts">
import { Mail, Phone, Briefcase, Search } from 'lucide-vue-next';
import type { ConsultancyRequest } from '@/types/consultancy-request';

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
    <div v-if="!requests.length" class="bg-surface-container-lowest border border-outline-variant/10 rounded-[2rem] p-16 text-center">
        <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mx-auto mb-6"><Search class="w-10 h-10 text-primary/40" /></div>
        <h3 class="text-xl font-bold text-on-surface mb-2">No se encontraron solicitudes</h3>
        <p class="text-on-surface-variant text-sm max-w-md mx-auto">No hay registros que coincidan con tu búsqueda o filtros actuales.</p>
    </div>

    <div v-else-if="viewMode === 'cards'" class="grid grid-cols-1 gap-6">
        <div v-for="req in requests" :key="req.id" class="bg-surface-container-lowest border border-outline-variant/10 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5" :class="{ 'bg-amber-400': req.status === 'pendiente', 'bg-blue-400': req.status === 'en_contacto', 'bg-green-400': req.status === 'cerrado' }"></div>
            <div class="flex flex-col lg:flex-row gap-8 items-start lg:items-center pl-4">
                <div class="flex-1 w-full">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border" :class="getStatusClasses(req.status)">{{ req.status }}</span>
                        <span class="text-xs text-on-surface-variant font-medium">Hace {{ new Date(req.created_at).toLocaleDateString() }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-on-surface mb-1">{{ req.name }}</h3>
                    <p class="text-sm font-medium text-primary mb-4">{{ req.area }}</p>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 mb-4">
                        <a :href="'mailto:' + req.email" class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors"><Mail class="w-4 h-4" /> {{ req.email }}</a>
                        <a v-if="req.phone" :href="'tel:' + req.phone" class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors"><Phone class="w-4 h-4" /> {{ req.phone }}</a>
                        <span v-if="req.company" class="flex items-center gap-2 text-sm text-on-surface-variant"><Briefcase class="w-4 h-4" /> {{ req.company }}</span>
                    </div>
                    <div class="bg-surface-container p-4 rounded-2xl text-sm text-on-surface leading-relaxed border border-outline-variant/10">
                        <span class="font-bold text-xs uppercase tracking-widest text-on-surface-variant mb-2 block">Mensaje:</span>
                        {{ req.message }}
                    </div>
                </div>
                <div class="flex flex-row lg:flex-col gap-3 w-full lg:w-48 border-t lg:border-t-0 lg:border-l border-outline-variant/10 pt-6 lg:pt-0 lg:pl-8">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant w-full hidden lg:block mb-2">Cambiar estado:</p>
                    <button @click="emit('updateStatus', req.id, 'pendiente')" class="flex-1 lg:w-full px-4 py-2.5 rounded-xl text-xs font-bold transition-colors border" :class="statusBtnClass(req.status, 'pendiente')">Pendiente</button>
                    <button @click="emit('updateStatus', req.id, 'en_contacto')" class="flex-1 lg:w-full px-4 py-2.5 rounded-xl text-xs font-bold transition-colors border" :class="statusBtnClass(req.status, 'en_contacto')">Contactado</button>
                    <button @click="emit('updateStatus', req.id, 'cerrado')" class="flex-1 lg:w-full px-4 py-2.5 rounded-xl text-xs font-bold transition-colors border" :class="statusBtnClass(req.status, 'cerrado')">Resuelto</button>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="bg-surface-container-lowest border border-outline-variant/10 rounded-[2rem] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-surface-container-low text-on-surface-variant font-bold uppercase tracking-widest text-[10px]">
                    <tr><th class="px-6 py-4">Estado / Fecha</th><th class="px-6 py-4">Prospecto</th><th class="px-6 py-4">Contacto</th><th class="px-6 py-4 min-w-[250px]">Mensaje</th><th class="px-6 py-4">Acciones</th></tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <tr v-for="req in requests" :key="req.id" class="hover:bg-surface-container-lowest/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest border inline-block w-max" :class="getStatusClasses(req.status)">{{ req.status }}</span>
                                <span class="text-xs text-on-surface-variant">{{ new Date(req.created_at).toLocaleDateString() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-on-surface text-sm">{{ req.name }}</p>
                            <p class="text-xs text-primary font-medium truncate max-w-[200px]" :title="req.area">{{ req.area }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-on-surface mb-1"><a :href="'mailto:'+req.email" class="hover:underline">{{ req.email }}</a></p>
                            <p class="text-xs text-on-surface-variant flex items-center gap-2">
                                <span v-if="req.phone"><Phone class="w-3 h-3 inline" /> {{ req.phone }}</span>
                                <span v-if="req.company"><Briefcase class="w-3 h-3 inline" /> {{ req.company }}</span>
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-normal min-w-[250px]"><p class="text-xs text-on-surface-variant line-clamp-2" :title="req.message">{{ req.message }}</p></td>
                        <td class="px-6 py-4">
                            <select :value="req.status" @change="(e) => emit('updateStatus', req.id, (e.target as HTMLSelectElement).value)" class="bg-surface-container text-xs font-bold rounded-xl px-3 py-2 border-none focus:ring-1 focus:ring-primary/30 outline-none cursor-pointer">
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
.line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; }
</style>
