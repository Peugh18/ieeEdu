<script setup lang="ts">
import { Search } from 'lucide-vue-next';

const search = defineModel<string>('search', { default: '' });
const role = defineModel<string>('role', { default: '' });
const status = defineModel<string>('status', { default: '' });
const perPage = defineModel<string>('perPage', { default: '20' });

const emit = defineEmits<{
    (e: 'filter'): void;
}>();
</script>

<template>
    <!-- Smart Filtering -->
    <div class="flex flex-col items-center gap-4 rounded-[2rem] border border-slate-100 bg-white/80 p-6 shadow-sm backdrop-blur-md lg:flex-row">
        <div class="group relative w-full lg:w-96">
            <Search class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary" />
            <input
                v-model="search"
                placeholder="Filtrar por nombre, DNI o email..."
                class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                @keydown.enter.prevent="emit('filter')"
            />
        </div>

        <div class="flex w-full flex-wrap items-center gap-3 lg:w-auto">
            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-2">
                <span class="pl-2 text-[10px] font-bold uppercase text-slate-400">Rol</span>
                <select v-model="role" class="h-10 cursor-pointer bg-transparent pr-8 text-sm font-semibold text-slate-700 outline-none">
                    <option value="">Cualquier Rol</option>
                    <option value="admin">Administrador</option>
                    <option value="usuario">Estudiante</option>
                </select>
            </div>

            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-2">
                <span class="pl-2 text-[10px] font-bold uppercase text-slate-400">Estado</span>
                <select v-model="status" class="h-10 cursor-pointer bg-transparent pr-8 text-sm font-semibold text-slate-700 outline-none">
                    <option value="">Cualquier Estado</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Baja</option>
                </select>
            </div>

            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-2">
                <select v-model="perPage" class="h-10 cursor-pointer bg-transparent px-2 text-sm font-semibold text-slate-700 outline-none">
                    <option value="10">10 por hoja</option>
                    <option value="20">20 por hoja</option>
                    <option value="50">50 por hoja</option>
                </select>
            </div>
        </div>
    </div>
</template>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
}
</style>
