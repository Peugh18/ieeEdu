<script setup lang="ts">
import { Crown, Search, XCircle, Clock, CreditCard, Plus, Activity, ChevronRight } from 'lucide-vue-next';
import type { SubscriptionUser } from '@/types/subscription';

const props = defineProps<{
    show: boolean;
    form: { user_id: string; type: string; months: number; amount: number; comprobante: File | null; errors: Record<string, string>; processing: boolean; reset: () => void; post: (url: string, opts: object) => void };
    usersResults: SubscriptionUser[];
    searchUserQuery: string;
    isSearchingUser: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'submit'): void;
    (e: 'search'): void;
    (e: 'selectUser', user: SubscriptionUser): void;
    (e: 'fileChange', event: Event): void;
}>();

function initials(name: string) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function getACls(id: number) {
    return ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700', 'bg-rose-50 text-rose-700'][id % 5];
}

function createPreviewUrl(file: File) {
    return URL.createObjectURL(file);
}
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-bounce">
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" @click.self="emit('close')">
                <div class="w-full max-w-xl rounded-[2.5rem] bg-white shadow-2xl overflow-hidden flex flex-col transition-all max-h-[95vh]">
                    <div class="bg-[#1a1a1a] p-10 text-white relative shrink-0">
                        <div class="absolute top-0 right-0 p-10 opacity-10"><Crown class="w-24 h-24" /></div>
                        <h2 class="font-serif text-3xl">Activar <span class="italic underline decoration-amber-500 underline-offset-8">Suscripción</span></h2>
                        <p class="mt-4 text-slate-400 text-xs font-bold uppercase tracking-[0.2em]">Habilita el acceso total a la plataforma</p>
                        <button @click="emit('close')" class="absolute top-8 right-8 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                            <XCircle class="h-6 w-6" />
                        </button>
                    </div>

                    <form @submit.prevent="emit('submit')" class="p-10 space-y-8 overflow-y-auto">
                        <!-- Student Search -->
                        <div class="space-y-3 relative z-[100]">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Identificar Estudiante *</label>
                            <div class="relative group">
                                <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-300 group-focus-within:text-primary transition-colors" />
                                <input v-model="searchUserQuery" @input="emit('search')" type="text" placeholder="Escribe para buscar por nombre o correo..."
                                    class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-6 text-sm font-bold text-slate-700 focus:border-primary outline-none transition-all shadow-inner" />
                                <div v-if="usersResults.length" class="absolute z-[999] left-0 right-0 mt-2 bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden max-h-60 overflow-y-auto">
                                    <button v-for="u in usersResults" :key="u.id" type="button" @click="emit('selectUser', u)"
                                        class="w-full flex items-center gap-4 p-5 hover:bg-amber-50/50 text-left border-b border-slate-50 last:border-0 transition-all group">
                                        <div :class="`w-10 h-10 rounded-xl flex items-center justify-center text-[11px] font-black ${getACls(u.id)} shadow-sm group-hover:scale-90 transition-transform`">
                                            {{ initials(u.name) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 group-hover:text-primary transition-colors leading-tight">{{ u.name }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">{{ u.email }}</p>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <p v-if="form.errors.user_id" class="px-1 text-[10px] font-bold text-rose-500 uppercase tracking-widest mt-1.5">{{ form.errors.user_id }}</p>
                        </div>

                        <!-- Plan Config -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Membresía *</label>
                                <div class="relative">
                                    <Crown class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-amber-500" />
                                    <select v-model="form.type" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-10 text-sm font-bold text-slate-700 focus:border-amber-500 appearance-none cursor-pointer outline-none transition-all">
                                        <option value="trimestral">Trimestral</option>
                                        <option value="semestral">Semestral</option>
                                        <option value="anual">Anual</option>
                                    </select>
                                </div>
                                <p v-if="form.errors.type" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.type }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Vigencia (Meses) *</label>
                                <div class="relative">
                                    <Clock class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-300" />
                                    <input v-model.number="form.months" type="number" min="1" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 px-4 text-sm font-bold text-slate-700 focus:border-amber-500 outline-none transition-all shadow-inner" />
                                </div>
                                <p v-if="form.errors.months" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.months }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Monto de Inversión (S/) *</label>
                                <div class="relative">
                                    <CreditCard class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-emerald-500" />
                                    <input v-model.number="form.amount" type="number" step="0.01" class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-11 px-4 text-sm font-bold text-slate-700 focus:border-emerald-500 outline-none transition-all shadow-inner" />
                                </div>
                                <p v-if="form.errors.amount" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.amount }}</p>
                            </div>
                        </div>

                        <div class="bg-amber-50/50 p-6 rounded-[2rem] border border-amber-100 flex gap-5 shadow-sm">
                            <div class="w-14 h-14 flex-shrink-0 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-amber-100 italic"><Crown class="w-7 h-7 text-amber-500" /></div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-700">Cláusula de Acceso Total</p>
                                <p class="text-xs text-amber-800 leading-relaxed font-serif italic font-medium">Al activar este plan, el estudiante tendrá acceso inmediato a <span class="underline decoration-amber-500/30">TODOS</span> los cursos grabados y beneficios del ecosistema por el tiempo estipulado.</p>
                            </div>
                        </div>

                        <!-- Capture Upload -->
                        <div class="space-y-3">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Soporte Financiero (Captura)</label>
                            <label class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 hover:border-emerald-300 hover:bg-emerald-50/30 transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-slate-300 shadow-sm border border-slate-100 group-hover:scale-110 transition-transform duration-500">
                                        <Activity v-if="form.comprobante" class="w-6 h-6 text-emerald-500" />
                                        <Plus v-else class="w-6 h-6" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-700 truncate max-w-[250px]">{{ form.comprobante ? form.comprobante.name : 'Vincular comprobante de pago' }}</p>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Formatos: PNG, JPG (Máx. 5MB)</p>
                                    </div>
                                </div>
                                <input type="file" accept="image/*" class="hidden" @change="emit('fileChange', $event)" />
                            </label>
                            <div v-if="form.comprobante" class="relative group mt-2 rounded-2xl overflow-hidden border border-emerald-100 shadow-sm">
                                <img :src="createPreviewUrl(form.comprobante)" class="max-h-32 w-full object-contain bg-slate-50" />
                            </div>
                            <p v-if="form.errors.comprobante" class="text-[9px] font-bold text-rose-500 uppercase mt-1">{{ form.errors.comprobante }}</p>
                        </div>

                        <div class="pt-4 flex gap-4">
                            <button type="button" @click="emit('close')" class="flex-1 h-14 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">Cancelar</button>
                            <button type="submit" :disabled="form.processing || !form.user_id"
                                class="flex-1 h-14 bg-[#1a1a1a] text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-slate-900/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-30 flex items-center justify-center gap-2">
                                {{ form.processing ? 'Sincronizando...' : 'Confirmar Activación' }}
                                <ChevronRight class="w-4 h-4" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-bounce-enter-active { animation: modal-bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-bounce-leave-active { animation: modal-bounce-in 0.3s reverse ease-in; }
@keyframes modal-bounce-in {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
</style>
