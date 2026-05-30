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
    <div class="bg-white/80 backdrop-blur-md rounded-[2rem] border border-slate-100 p-6 flex flex-col lg:flex-row items-center gap-4 shadow-sm">
        <div class="relative w-full lg:w-96 group">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-primary transition-colors" />
            <input
                v-model="search"
                placeholder="Filtrar por nombre, DNI o email..."
                class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all"
                @keydown.enter.prevent="emit('filter')"
            />
        </div>
        
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <div class="flex items-center gap-2 bg-slate-50 px-2 rounded-2xl border border-slate-200">
                <span class="text-[10px] font-bold uppercase text-slate-400 pl-2">Rol</span>
                <select v-model="role" class="h-10 bg-transparent text-sm font-semibold text-slate-700 pr-8 outline-none cursor-pointer">
                    <option value="">Cualquier Rol</option>
                    <option value="admin">Administrador</option>
                    <option value="usuario">Estudiante</option>
                </select>
            </div>

            <div class="flex items-center gap-2 bg-slate-50 px-2 rounded-2xl border border-slate-200">
                <span class="text-[10px] font-bold uppercase text-slate-400 pl-2">Estado</span>
                <select v-model="status" class="h-10 bg-transparent text-sm font-semibold text-slate-700 pr-8 outline-none cursor-pointer">
                    <option value="">Cualquier Estado</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Baja</option>
                </select>
            </div>

            <div class="flex items-center gap-2 bg-slate-50 px-2 rounded-2xl border border-slate-200">
                <select v-model="perPage" class="h-10 bg-transparent text-sm font-semibold text-slate-700 px-2 outline-none cursor-pointer">
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
