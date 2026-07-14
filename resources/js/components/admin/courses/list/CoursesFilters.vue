<script setup lang="ts">
import { Search } from 'lucide-vue-next';
import AppSelect from '@/components/ui/AppSelect.vue';

const search = defineModel<string>('search', { default: '' });
const status = defineModel<string>('status', { default: '' });
const type = defineModel<string>('type', { default: '' });
const perPage = defineModel<string>('perPage', { default: '10' });

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
                placeholder="Buscar curso por título..."
                class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                @keydown.enter.prevent="emit('filter')"
            />
        </div>

        <div class="flex w-full flex-wrap items-center gap-3 lg:w-auto">
            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-2">
                <span class="pl-2 text-[10px] font-bold uppercase text-slate-400">Estado</span>
                <AppSelect 
                    v-model="status" 
                    :options="[
                        { value: '', label: 'Cualquier Estado' },
                        { value: 'BORRADOR', label: 'Borrador' },
                        { value: 'PUBLICADO', label: 'Publicado' },
                        { value: 'OCULTO', label: 'Oculto' }
                    ]"
                    class="h-10 border-0 bg-transparent shadow-none" 
                />
            </div>

            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-2">
                <span class="pl-2 text-[10px] font-bold uppercase text-slate-400">Tipo</span>
                <AppSelect 
                    v-model="type" 
                    :options="[
                        { value: '', label: 'Cualquier Tipo' },
                        { value: 'grabado', label: 'Grabado' },
                        { value: 'en vivo', label: 'En vivo' },
                        { value: 'evento', label: 'Masterclass' }
                    ]"
                    class="h-10 border-0 bg-transparent shadow-none" 
                />
            </div>

            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-2">
                <AppSelect 
                    v-model="perPage" 
                    :options="[
                        { value: '10', label: '10 por hoja' },
                        { value: '20', label: '20 por hoja' },
                        { value: '50', label: '50 por hoja' }
                    ]"
                    class="h-10 border-0 bg-transparent shadow-none w-[130px]" 
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>
