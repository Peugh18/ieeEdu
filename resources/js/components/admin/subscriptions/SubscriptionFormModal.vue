<script setup lang="ts">
import type { SubscriptionUser } from '@/types/subscription';
import { Activity, ChevronRight, Clock, CreditCard, Crown, Plus, Search, XCircle } from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    planOptions?: { slug: string; name: string; price: number; months: number }[];
    form: {
        user_id: string;
        type: string;
        months: number;
        amount: number;
        comprobante: File | null;
        errors: Record<string, string>;
        processing: boolean;
        reset: () => void;
        post: (url: string, opts: object) => void;
    };
    usersResults: SubscriptionUser[];
    searchUserQuery: string;
    isSearchingUser: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'submit'): void;
    (e: 'search'): void;
    (e: 'update:searchUserQuery', value: string): void;
    (e: 'selectUser', user: SubscriptionUser): void;
    (e: 'fileChange', event: Event): void;
}>();

function onSearchInput(event: Event) {
    const value = (event.target as HTMLInputElement).value;
    emit('update:searchUserQuery', value);
    emit('search');
}

function initials(name: string) {
    return name
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
}

function getACls(id: number) {
    return [
        'bg-indigo-50 text-indigo-700',
        'bg-blue-50 text-blue-700',
        'bg-emerald-50 text-emerald-700',
        'bg-amber-50 text-amber-700',
        'bg-rose-50 text-rose-700',
    ][id % 5];
}

function createPreviewUrl(file: File) {
    return URL.createObjectURL(file);
}
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-bounce">
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="emit('close')"
            >
                <div class="flex max-h-[95vh] w-full max-w-xl flex-col overflow-hidden rounded-[2.5rem] bg-white shadow-2xl transition-all">
                    <div class="relative shrink-0 bg-[#1a1a1a] p-10 text-white">
                        <div class="absolute right-0 top-0 p-10 opacity-10"><Crown class="h-24 w-24" /></div>
                        <h2 class="font-serif text-3xl">
                            Activar <span class="italic underline decoration-amber-500 underline-offset-8">Suscripción</span>
                        </h2>
                        <p class="mt-4 text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Habilita el acceso total a la plataforma</p>
                        <button
                            @click="emit('close')"
                            class="absolute right-8 top-8 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20"
                        >
                            <XCircle class="h-6 w-6" />
                        </button>
                    </div>

                    <form @submit.prevent="emit('submit')" class="space-y-8 overflow-y-auto p-10">
                        <!-- Student Search -->
                        <div class="relative z-[100] space-y-3">
                            <label class="block px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                >Identificar Estudiante *</label
                            >
                            <div class="group relative">
                                <Search
                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-300 transition-colors group-focus-within:text-primary"
                                />
                                <input
                                    :value="searchUserQuery"
                                    type="text"
                                    placeholder="Escribe para buscar por nombre o correo..."
                                    @input="onSearchInput"
                                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-11 pr-6 text-sm font-bold text-slate-700 shadow-inner outline-none transition-all focus:border-primary"
                                />
                                <div
                                    v-if="usersResults.length"
                                    class="absolute left-0 right-0 z-[999] mt-2 max-h-60 overflow-hidden overflow-y-auto rounded-2xl border border-slate-100 bg-white shadow-2xl"
                                >
                                    <button
                                        v-for="u in usersResults"
                                        :key="u.id"
                                        type="button"
                                        @click="emit('selectUser', u)"
                                        class="group flex w-full items-center gap-4 border-b border-slate-50 p-5 text-left transition-all last:border-0 hover:bg-amber-50/50"
                                    >
                                        <div
                                            :class="`flex h-10 w-10 items-center justify-center rounded-xl text-[11px] font-black ${getACls(u.id)} shadow-sm transition-transform group-hover:scale-90`"
                                        >
                                            {{ initials(u.name) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary">
                                                {{ u.name }}
                                            </p>
                                            <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">{{ u.email }}</p>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <p v-if="form.errors.user_id" class="mt-1.5 px-1 text-[10px] font-bold uppercase tracking-widest text-rose-500">
                                {{ form.errors.user_id }}
                            </p>
                        </div>

                        <!-- Plan Config -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="space-y-2">
                                <label class="block px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Membresía *</label>
                                <div class="relative">
                                    <Crown class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-amber-500" />
                                    <select
                                        v-model="form.type"
                                        class="h-14 w-full cursor-pointer appearance-none rounded-2xl border border-slate-200 bg-slate-50 pl-11 pr-10 text-sm font-bold text-slate-700 outline-none transition-all focus:border-amber-500"
                                    >
                                        <option v-for="plan in planOptions ?? []" :key="plan.slug" :value="plan.slug">{{ plan.name }}</option>
                                    </select>
                                </div>
                                <p v-if="form.errors.type" class="mt-1 text-[9px] font-bold uppercase text-rose-500">{{ form.errors.type }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="block px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Vigencia (Meses) *</label
                                >
                                <div class="relative">
                                    <Clock class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-300" />
                                    <input
                                        v-model.number="form.months"
                                        type="number"
                                        min="1"
                                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pl-11 text-sm font-bold text-slate-700 shadow-inner outline-none transition-all focus:border-amber-500"
                                    />
                                </div>
                                <p v-if="form.errors.months" class="mt-1 text-[9px] font-bold uppercase text-rose-500">{{ form.errors.months }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="block px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Monto de Inversión (S/) *</label
                                >
                                <div class="relative">
                                    <CreditCard class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-500" />
                                    <input
                                        v-model.number="form.amount"
                                        type="number"
                                        step="0.01"
                                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pl-11 text-sm font-bold text-slate-700 shadow-inner outline-none transition-all focus:border-emerald-500"
                                    />
                                </div>
                                <p v-if="form.errors.amount" class="mt-1 text-[9px] font-bold uppercase text-rose-500">{{ form.errors.amount }}</p>
                            </div>
                        </div>

                        <div class="flex gap-5 rounded-[2rem] border border-amber-100 bg-amber-50/50 p-6 shadow-sm">
                            <div
                                class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl border border-amber-100 bg-white italic shadow-sm"
                            >
                                <Crown class="h-7 w-7 text-amber-500" />
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-700">Cláusula de Acceso Total</p>
                                <p class="font-serif text-xs font-medium italic leading-relaxed text-amber-800">
                                    Al activar este plan, el estudiante tendrá acceso inmediato a
                                    <span class="underline decoration-amber-500/30">TODOS</span> los cursos grabados y beneficios del ecosistema por
                                    el tiempo estipulado.
                                </p>
                            </div>
                        </div>

                        <!-- Capture Upload -->
                        <div class="space-y-3">
                            <label class="block px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                >Soporte Financiero (Captura)</label
                            >
                            <label
                                class="group flex cursor-pointer items-center justify-between gap-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 transition-all hover:border-emerald-300 hover:bg-emerald-50/30"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-100 bg-white text-slate-300 shadow-sm transition-transform duration-500 group-hover:scale-110"
                                    >
                                        <Activity v-if="form.comprobante" class="h-6 w-6 text-emerald-500" />
                                        <Plus v-else class="h-6 w-6" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="max-w-[250px] truncate text-sm font-bold text-slate-700">
                                            {{ form.comprobante ? form.comprobante.name : 'Vincular comprobante de pago' }}
                                        </p>
                                        <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Formatos: PNG, JPG (Máx. 5MB)</p>
                                    </div>
                                </div>
                                <input type="file" accept="image/*" class="hidden" @change="emit('fileChange', $event)" />
                            </label>
                            <div v-if="form.comprobante" class="group relative mt-2 overflow-hidden rounded-2xl border border-emerald-100 shadow-sm">
                                <img :src="createPreviewUrl(form.comprobante)" class="max-h-32 w-full bg-slate-50 object-contain" />
                            </div>
                            <p v-if="form.errors.comprobante" class="mt-1 text-[9px] font-bold uppercase text-rose-500">
                                {{ form.errors.comprobante }}
                            </p>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button
                                type="button"
                                @click="emit('close')"
                                class="h-14 flex-1 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-600"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing || !form.user_id"
                                class="flex h-14 flex-1 items-center justify-center gap-2 rounded-2xl bg-[#1a1a1a] text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-slate-900/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-30"
                            >
                                {{ form.processing ? 'Sincronizando...' : 'Confirmar Activación' }}
                                <ChevronRight class="h-4 w-4" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-bounce-enter-active {
    animation: modal-bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-bounce-leave-active {
    animation: modal-bounce-in 0.3s reverse ease-in;
}
@keyframes modal-bounce-in {
    0% {
        transform: scale(0.9);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
</style>
