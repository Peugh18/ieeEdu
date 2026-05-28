<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Mail, Phone, Briefcase, ChevronDown, CheckCircle, Clock, LayoutGrid, List } from 'lucide-vue-next';

const props = defineProps<{
    requests: any;
    filters: { search?: string; status?: string };
    stats: { total: number; pending: number; contacted: number; resolved: number };
}>();

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');

let searchTimeout: any;
const viewMode = ref('cards');
watch([search, statusFilter], ([newSearch, newStatus]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            route('admin.consultancies.index'),
            { search: newSearch, status: newStatus },
            { preserveState: true, replace: true }
        );
    }, 300);
});

const updateStatus = (id: number, status: string) => {
    router.patch(route('admin.consultancies.status', id), { status }, {
        preserveScroll: true,
    });
};

const getStatusClasses = (status: string) => {
    if (status === 'pendiente') return 'bg-amber-100 text-amber-700 border-amber-200';
    if (status === 'en_contacto') return 'bg-blue-100 text-blue-700 border-blue-200';
    if (status === 'cerrado') return 'bg-green-100 text-green-700 border-green-200';
    return 'bg-gray-100 text-gray-700 border-gray-200';
};
</script>

<template>
    <Head title="Solicitudes de Consultoría | Admin" />

    <AppLayout>
        <div class="px-8 py-10 max-w-7xl mx-auto min-h-screen">
            <div class="mb-10">
                <h1 class="text-4xl font-serif font-bold tracking-tight text-on-surface mb-2">
                    Solicitudes de <span class="italic text-primary">Consultoría</span>
                </h1>
                <p class="text-on-surface-variant text-sm font-medium">Gestiona y haz seguimiento de las empresas interesadas en nuestros servicios.</p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border border-outline-variant/10 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center">
                        <Briefcase class="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total</p>
                        <p class="text-3xl font-bold text-on-surface">{{ stats.total }}</p>
                    </div>
                </div>
                <div class="bg-amber-50 rounded-3xl p-6 shadow-sm border border-amber-100 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center">
                        <Clock class="w-6 h-6 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-amber-800 uppercase tracking-widest mb-1">Pendientes</p>
                        <p class="text-3xl font-bold text-amber-900">{{ stats.pending }}</p>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-3xl p-6 shadow-sm border border-blue-100 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center">
                        <Phone class="w-6 h-6 text-blue-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-blue-800 uppercase tracking-widest mb-1">Contactados</p>
                        <p class="text-3xl font-bold text-blue-900">{{ stats.contacted }}</p>
                    </div>
                </div>
                <div class="bg-green-50 rounded-3xl p-6 shadow-sm border border-green-100 flex items-center gap-5">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center">
                        <CheckCircle class="w-6 h-6 text-green-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-green-800 uppercase tracking-widest mb-1">Resueltos</p>
                        <p class="text-3xl font-bold text-green-900">{{ stats.resolved }}</p>
                    </div>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="bg-surface-container-lowest p-4 rounded-[2rem] border border-outline-variant/10 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center mb-8">
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <Search class="h-4 w-4 text-on-surface-variant/50" />
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre, correo o empresa..."
                        class="w-full pl-10 pr-4 py-3 rounded-2xl bg-surface-container-highest/50 border-none focus:ring-2 focus:ring-primary/20 text-sm font-medium placeholder:text-on-surface-variant/50 transition-all"
                    >
                </div>
                
                <div class="flex gap-2 w-full md:w-auto">
                    <select v-model="statusFilter" class="w-full md:w-auto bg-surface-container-highest/50 border-none rounded-2xl px-5 py-3 text-sm font-bold text-on-surface focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer appearance-none pr-10 relative">
                        <option value="">Ocultar resueltos</option>
                        <option value="all">Todos los estados</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_contacto">Contactado</option>
                        <option value="cerrado">Resuelto</option>
                    </select>

                    <div class="hidden md:flex bg-surface-container-highest/30 p-1 rounded-2xl">
                        <button 
                            @click="viewMode = 'cards'"
                            class="px-3 py-2 rounded-xl transition-all"
                            :class="viewMode === 'cards' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:text-on-surface'"
                        >
                            <LayoutGrid class="w-4 h-4" />
                        </button>
                        <button 
                            @click="viewMode = 'list'"
                            class="px-3 py-2 rounded-xl transition-all"
                            :class="viewMode === 'list' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:text-on-surface'"
                        >
                            <List class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Listado -->
            <div v-if="requests.data.length === 0" class="bg-surface-container-lowest border border-outline-variant/10 rounded-[2rem] p-16 text-center">
                <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <Search class="w-10 h-10 text-primary/40" />
                </div>
                <h3 class="text-xl font-bold text-on-surface mb-2">No se encontraron solicitudes</h3>
                <p class="text-on-surface-variant text-sm max-w-md mx-auto">No hay registros que coincidan con tu búsqueda o filtros actuales.</p>
            </div>

            <div v-else-if="viewMode === 'cards'" class="grid grid-cols-1 gap-6">
                <div v-for="req in requests.data" :key="req.id" class="bg-surface-container-lowest border border-outline-variant/10 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5" :class="{
                        'bg-amber-400': req.status === 'pendiente',
                        'bg-blue-400': req.status === 'en_contacto',
                        'bg-green-400': req.status === 'cerrado'
                    }"></div>

                    <div class="flex flex-col lg:flex-row gap-8 items-start lg:items-center pl-4">
                        <div class="flex-1 w-full">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border" :class="getStatusClasses(req.status)">
                                    {{ req.status }}
                                </span>
                                <span class="text-xs text-on-surface-variant font-medium">Hace {{ new Date(req.created_at).toLocaleDateString() }}</span>
                            </div>

                            <h3 class="text-xl font-bold text-on-surface mb-1">{{ req.name }}</h3>
                            <p class="text-sm font-medium text-primary mb-4">{{ req.area }}</p>

                            <div class="flex flex-wrap gap-x-6 gap-y-2 mb-4">
                                <a :href="'mailto:' + req.email" class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors">
                                    <Mail class="w-4 h-4" /> {{ req.email }}
                                </a>
                                <a v-if="req.phone" :href="'tel:' + req.phone" class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-primary transition-colors">
                                    <Phone class="w-4 h-4" /> {{ req.phone }}
                                </a>
                                <span v-if="req.company" class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <Briefcase class="w-4 h-4" /> {{ req.company }}
                                </span>
                            </div>

                            <div class="bg-surface-container p-4 rounded-2xl text-sm text-on-surface leading-relaxed border border-outline-variant/10">
                                <span class="font-bold text-xs uppercase tracking-widest text-on-surface-variant mb-2 block">Mensaje:</span>
                                {{ req.message }}
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex flex-row lg:flex-col gap-3 w-full lg:w-48 border-t lg:border-t-0 lg:border-l border-outline-variant/10 pt-6 lg:pt-0 lg:pl-8">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant w-full hidden lg:block mb-2">Cambiar estado:</p>
                            <button @click="updateStatus(req.id, 'pendiente')" class="flex-1 lg:w-full px-4 py-2.5 rounded-xl text-xs font-bold transition-colors border" :class="req.status === 'pendiente' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-transparent text-on-surface-variant border-outline-variant/20 hover:bg-surface-container-high'">
                                Pendiente
                            </button>
                            <button @click="updateStatus(req.id, 'en_contacto')" class="flex-1 lg:w-full px-4 py-2.5 rounded-xl text-xs font-bold transition-colors border" :class="req.status === 'en_contacto' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-transparent text-on-surface-variant border-outline-variant/20 hover:bg-surface-container-high'">
                                Contactado
                            </button>
                            <button @click="updateStatus(req.id, 'cerrado')" class="flex-1 lg:w-full px-4 py-2.5 rounded-xl text-xs font-bold transition-colors border" :class="req.status === 'cerrado' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-transparent text-on-surface-variant border-outline-variant/20 hover:bg-surface-container-high'">
                                Resuelto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else-if="viewMode === 'list'" class="bg-surface-container-lowest border border-outline-variant/10 rounded-[2rem] overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-surface-container-low text-on-surface-variant font-bold uppercase tracking-widest text-[10px]">
                            <tr>
                                <th class="px-6 py-4">Estado / Fecha</th>
                                <th class="px-6 py-4">Prospecto</th>
                                <th class="px-6 py-4">Contacto</th>
                                <th class="px-6 py-4 min-w-[250px]">Mensaje</th>
                                <th class="px-6 py-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            <tr v-for="req in requests.data" :key="req.id" class="hover:bg-surface-container-lowest/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest border inline-block w-max" :class="getStatusClasses(req.status)">
                                            {{ req.status }}
                                        </span>
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
                                <td class="px-6 py-4 whitespace-normal min-w-[250px]">
                                    <p class="text-xs text-on-surface-variant line-clamp-2" :title="req.message">{{ req.message }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <select 
                                        :value="req.status"
                                        @change="(e) => updateStatus(req.id, (e.target as HTMLSelectElement).value)"
                                        class="bg-surface-container text-xs font-bold rounded-xl px-3 py-2 border-none focus:ring-1 focus:ring-primary/30 outline-none cursor-pointer"
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

            <!-- Pagination -->
            <div class="mt-10 flex justify-center" v-if="requests.last_page > 1">
                <div class="flex items-center gap-2 bg-surface-container-lowest p-2 rounded-2xl shadow-sm border border-outline-variant/10">
                    <Link
                        v-for="link in requests.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        class="px-4 py-2 rounded-xl text-sm font-bold transition-colors"
                        :class="[
                            link.active ? 'bg-primary text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
