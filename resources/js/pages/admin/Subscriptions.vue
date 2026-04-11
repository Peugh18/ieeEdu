<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Crown, Search, Plus, Filter, MoreVertical, 
    Calendar, User, CheckCircle2, XCircle, Trash2,
    Check, RotateCw
} from 'lucide-vue-next';
import axios from 'axios';

interface Subscription {
    id: number;
    user: {
        id: number;
        name: string;
        email: string;
    };
    type: string;
    start_date: string;
    end_date: string;
    status: 'activa' | 'expirada' | 'cancelada';
}

interface Props {
    subscriptions: {
        data: Subscription[];
        links: any[];
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const showAddModal = ref(false);
const usersResults = ref<any[]>([]);
const searchUserQuery = ref('');
const isSearchingUser = ref(false);

const form = useForm({
    user_id: '',
    type: 'Trimestral',
    months: 3,
});

const searchUsers = async () => {
    if (searchUserQuery.value.length < 3) return;
    isSearchingUser.value = true;
    try {
        const res = await axios.get(route('admin.users.search'), { params: { q: searchUserQuery.value } });
        usersResults.value = res.data;
    } catch (e) {
        console.error(e);
    } finally {
        isSearchingUser.value = false;
    }
};

const selectUser = (user: any) => {
    form.user_id = user.id;
    searchUserQuery.value = user.name;
    usersResults.value = [];
};

const submit = () => {
    form.post(route('admin.subscriptions.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            searchUserQuery.value = '';
        }
    });
};

const getStatusStyles = (status: string) => {
    switch (status) {
        case 'activa': return 'bg-emerald-50 text-emerald-700 border-emerald-100';
        case 'expirada': return 'bg-rose-50 text-rose-700 border-rose-100';
        default: return 'bg-gray-100 text-gray-600 border-gray-200';
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('admin.dashboard') },
    { title: 'Suscripciones Premium', href: '#' },
];
</script>

<template>
    <Head title="Suscripciones Premium - Admin" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 md:p-10 max-w-7xl mx-auto space-y-8">
            
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-serif font-bold text-gray-900">Suscripciones Premium</h1>
                    <p class="text-gray-500 text-sm mt-1">Gestión de membresías y acceso total a la plataforma.</p>
                </div>
                <button 
                    @click="showAddModal = true"
                    class="inline-flex items-center gap-2 bg-[#57572A] text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-xl shadow-[#57572A]/20 hover:scale-105 active:scale-95 transition-all"
                >
                    <Plus class="w-5 h-5" />
                    Nueva Suscripción
                </button>
            </header>

            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4 bg-white p-4 rounded-3xl border border-outline-variant/30 shadow-sm items-center">
                <div class="relative flex-1 w-full">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input 
                        type="text" 
                        placeholder="Buscar por usuario o email..."
                        class="w-full pl-11 pr-4 py-3 bg-surface rounded-2xl border-none focus:ring-2 focus:ring-[#57572A] text-sm"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button class="p-3 bg-white border border-outline-variant/30 rounded-2xl text-gray-500 hover:bg-gray-50 transition-colors">
                        <Filter class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-[2.5rem] border border-outline-variant/30 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-outline-variant/20">
                                <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estudiante</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Plan</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periodo</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Estado</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            <tr v-for="sub in subscriptions.data" :key="sub.id" class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold shadow-inner">
                                            {{ sub.user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 leading-none">{{ sub.user.name }}</p>
                                            <p class="text-xs text-gray-400 mt-1.5">{{ sub.user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-gray-700">
                                        <Crown class="w-4 h-4 text-amber-500" />
                                        {{ sub.type }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col text-xs text-gray-500">
                                        <div class="flex items-center gap-1.5">
                                            <Calendar class="w-3 h-3 opacity-50" />
                                            <span>{{ new Date(sub.start_date).toLocaleDateString() }} - {{ new Date(sub.end_date).toLocaleDateString() }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="px-3 py-1.5 rounded-full border text-[10px] font-bold uppercase tracking-widest shadow-sm" :class="getStatusStyles(sub.status)">
                                        {{ sub.status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link 
                                            :href="route('admin.subscriptions.toggle', sub.id)" 
                                            method="patch" as="button"
                                            class="p-2.5 bg-white border border-outline-variant/30 rounded-xl text-gray-400 hover:text-primary transition-colors hover:border-primary/20"
                                            title="Cambiar Estado"
                                        >
                                            <RotateCw class="w-4 h-4" />
                                        </Link>
                                        <Link 
                                            :href="route('admin.subscriptions.destroy', sub.id)" 
                                            method="delete" as="button"
                                            class="p-2.5 bg-white border border-outline-variant/30 rounded-xl text-gray-400 hover:text-red-600 transition-colors hover:border-red-100"
                                            title="Eliminar"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Add -->
            <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div class="bg-white rounded-[2.5rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in zoom-in duration-300">
                    <header class="p-8 bg-gray-900 text-white relative">
                        <h2 class="text-2xl font-serif font-bold">Activar Suscripción Premium</h2>
                        <p class="text-gray-400 text-sm mt-1 leading-relaxed italic">Habilita el acceso total a todos los cursos para un estudiante.</p>
                        <button @click="showAddModal = false" class="absolute top-8 right-8 text-white/50 hover:text-white transition-colors">
                            <XCircle class="w-6 h-6" />
                        </button>
                    </header>
                    
                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        <!-- User Search -->
                        <div class="space-y-3 relative">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Seleccionar Estudiante</label>
                            <div class="relative">
                                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input 
                                    v-model="searchUserQuery"
                                    type="text" 
                                    placeholder="Nombre o email..."
                                    @input="searchUsers"
                                    class="w-full pl-11 pr-4 py-4 bg-gray-50 rounded-2xl border-none focus:ring-2 focus:ring-[#57572A] text-sm"
                                />
                            </div>
                            
                            <div v-if="usersResults.length > 0" class="absolute z-20 top-full left-0 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-outline-variant/30 overflow-hidden max-h-60 overflow-y-auto">
                                <button 
                                    v-for="u in usersResults" :key="u.id"
                                    type="button"
                                    @click="selectUser(u)"
                                    class="w-full flex items-center gap-4 p-4 hover:bg-primary/5 text-left border-b border-outline-variant/10 transition-colors"
                                >
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold">{{ u.name.charAt(0) }}</div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ u.name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ u.email }}</p>
                                    </div>
                                    <Plus class="ml-auto w-4 h-4 text-gray-300" />
                                </button>
                            </div>
                        </div>

                        <!-- Package Select -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tipo de Plan</label>
                                <select v-model="form.type" class="w-full py-4 bg-gray-50 rounded-2xl border-none focus:ring-2 focus:ring-[#57572A] text-sm">
                                    <option value="Trimestral">Trimestral (Trimestral)</option>
                                    <option value="Semestral">Semestral</option>
                                    <option value="Anual">Anual (Completo)</option>
                                </select>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Duración (Meses)</label>
                                <input v-model.number="form.months" type="number" min="1" class="w-full py-4 bg-gray-50 rounded-2xl border-none focus:ring-2 focus:ring-[#57572A] text-sm" />
                            </div>
                        </div>

                        <div class="bg-amber-50 p-6 rounded-3xl border border-amber-100 flex gap-4">
                            <div class="p-3 bg-white rounded-2xl"><Crown class="w-6 h-6 text-amber-500" /></div>
                            <p class="text-xs text-amber-800 leading-relaxed font-serif italic">
                                <strong>Nota importante:</strong> Al activar este plan, el estudiante tendrá acceso inmediato a **TODOS** los cursos grabados y beneficios premium por el tiempo estipulado.
                            </p>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button 
                                type="button" @click="showAddModal = false"
                                class="flex-1 py-4 text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                :disabled="form.processing || !form.user_id"
                                class="flex-1 py-4 bg-[#57572A] text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-[#57572A]/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50"
                            >
                                {{ form.processing ? 'Procesando...' : 'Confirmar Activación' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
