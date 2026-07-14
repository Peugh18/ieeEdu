<script setup lang="ts">
import { useForm as useInertiaForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ChevronRight, FileImage, Plus, Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import AppSelect from '@/components/ui/AppSelect.vue';

interface CourseOption {
    id: number;
    title: string;
    price: number;
    sale_price: number | null;
}

interface BookOption {
    id: number;
    title: string;
    price: number | string;
}

interface UserResult {
    id: number;
    name: string;
    email: string;
}

interface PlanOption {
    slug: string;
    name: string;
    price: number;
    months: number;
}

const props = defineProps<{
    show: boolean;
    courses: CourseOption[];
    books: BookOption[];
    planOptions?: PlanOption[];
    initialSearch?: string;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const productType = ref<'course' | 'book' | 'membership'>('course');

const createForm = useInertiaForm({
    user_id: 0,
    product_type: 'course' as 'course' | 'book' | 'membership',
    course_id: '' as string | number,
    book_id: '' as string | number,
    subscription_type: '',
    amount: '',
    status: 'en_revision',
    comprobante: null as File | null,
});

const userQuery = ref('');
const userResults = ref<UserResult[]>([]);
const selectedUser = ref<UserResult | null>(null);
const showDropdown = ref(false);
let userTimer: ReturnType<typeof setTimeout> | undefined;
let isSelecting = false;

function onUserFocus() {
    if (userResults.value.length) showDropdown.value = true;
}

function onUserBlur() {
    setTimeout(() => {
        showDropdown.value = false;
    }, 200);
}

watch(userQuery, (val) => {
    if (isSelecting) {
        isSelecting = false;
        return;
    }
    clearTimeout(userTimer);
    selectedUser.value = null;
    createForm.user_id = 0;
    if (val.length < 2) {
        userResults.value = [];
        showDropdown.value = false;
        return;
    }
    userTimer = setTimeout(async () => {
        const { data } = await axios.get(route('admin.users.search'), { params: { q: val } });
        userResults.value = data;
        showDropdown.value = true;
    }, 300);
});

function selectUser(u: UserResult) {
    isSelecting = true;
    selectedUser.value = u;
    createForm.user_id = u.id;
    userQuery.value = u.name + ' — ' + u.email;
    showDropdown.value = false;
}

watch(productType, (type) => {
    createForm.product_type = type;
    createForm.course_id = '';
    createForm.book_id = '';
    createForm.subscription_type = '';
    createForm.amount = '';
});

watch(
    () => createForm.course_id,
    (id) => {
        if (productType.value !== 'course') return;
        const c = props.courses.find((c) => String(c.id) === String(id));
        if (c) createForm.amount = String(c.sale_price && c.sale_price < c.price ? c.sale_price : c.price);
    },
);

watch(
    () => createForm.book_id,
    (id) => {
        if (productType.value !== 'book') return;
        const b = props.books.find((b) => String(b.id) === String(id));
        if (b) createForm.amount = String(b.price);
    },
);

watch(
    () => createForm.subscription_type,
    (slug) => {
        if (productType.value !== 'membership') return;
        const p = props.planOptions?.find((p) => p.slug === slug);
        if (p) createForm.amount = String(p.price);
    },
);

function onFileChange(e: Event) {
    const el = e.target as HTMLInputElement;
    createForm.comprobante = el.files?.[0] ?? null;
}

function resetCreate() {
    productType.value = 'course';
    createForm.reset();
    createForm.product_type = 'course';
    createForm.clearErrors();
    userQuery.value = '';
    selectedUser.value = null;
    userResults.value = [];
    showDropdown.value = false;
}

function submitCreate() {
    if (!createForm.user_id) {
        createForm.setError('user_id', 'Selecciona un estudiante.');
        return;
    }
    createForm.post(route('admin.payments.store'), {
        forceFormData: true,
        onSuccess: () => {
            resetCreate();
            emit('close');
        },
        preserveScroll: true,
    });
}

function handleClose() {
    resetCreate();
    emit('close');
}

watch(
    () => props.show,
    (isOpen) => {
        if (isOpen && props.initialSearch) {
            userQuery.value = props.initialSearch;
        }
    },
);

const createPreviewUrl = (file: File) => URL.createObjectURL(file);
const initials = (n: string) =>
    n
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
const avatarColors = [
    'bg-indigo-50 text-indigo-700',
    'bg-blue-50 text-blue-700',
    'bg-emerald-50 text-emerald-700',
    'bg-amber-50 text-amber-700',
    'bg-rose-50 text-rose-700',
];
const aCls = (id: number) => avatarColors[id % avatarColors.length];

const canSubmit = () => {
    if (!createForm.user_id || !createForm.amount) return false;
    if (productType.value === 'course') return !!createForm.course_id;
    if (productType.value === 'book') return !!createForm.book_id;
    if (productType.value === 'membership') return !!createForm.subscription_type;
    return false;
};
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-bounce">
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
                <div class="flex max-h-[95vh] w-full max-w-lg flex-col overflow-hidden rounded-[2.5rem] bg-white dark:bg-[#1a1a1a] shadow-2xl">
                    <div class="relative shrink-0 bg-primary p-8 text-white">
                        <div class="absolute right-0 top-0 p-8 opacity-10"><Plus class="h-24 w-24" /></div>
                        <h2 class="font-serif text-3xl">
                            Registrar <span class="italic underline decoration-white/20 underline-offset-8">Venta</span>
                        </h2>
                        <p class="mt-2 text-sm italic text-outline-variant">Curso, libro o membresía</p>
                        <button
                            @click="handleClose"
                            class="absolute right-6 top-6 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>
                    <div class="space-y-6 overflow-y-auto p-8">
                        <div class="flex gap-2 rounded-2xl bg-slate-100 dark:bg-slate-800 p-1">
                            <button
                                type="button"
                                class="flex-1 rounded-xl py-2.5 text-xs font-bold transition-all"
                                :class="productType === 'course' ? 'bg-white dark:bg-slate-700 text-primary dark:text-white shadow' : 'text-slate-500 dark:text-slate-400'"
                                @click="productType = 'course'"
                            >
                                Curso
                            </button>
                            <button
                                type="button"
                                class="flex-1 rounded-xl py-2.5 text-xs font-bold transition-all"
                                :class="productType === 'book' ? 'bg-white dark:bg-slate-700 text-primary dark:text-white shadow' : 'text-slate-500 dark:text-slate-400'"
                                @click="productType = 'book'"
                            >
                                Libro
                            </button>
                            <button
                                type="button"
                                class="flex-1 rounded-xl py-2.5 text-xs font-bold transition-all"
                                :class="productType === 'membership' ? 'bg-white dark:bg-slate-700 text-primary dark:text-white shadow' : 'text-slate-500 dark:text-slate-400'"
                                @click="productType = 'membership'"
                            >
                                Membresía
                            </button>
                        </div>

                        <div class="relative">
                            <label class="mb-2 block px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estudiante *</label>
                            <div class="group relative">
                                <Search
                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-primary"
                                />
                                <input
                                    v-model="userQuery"
                                    placeholder="Buscar por nombre o email..."
                                    @focus="onUserFocus"
                                    @blur="onUserBlur"
                                    class="h-14 w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 pl-12 pr-6 text-sm font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-primary"
                                />
                                <span
                                    v-if="selectedUser"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 rounded-lg bg-emerald-50 px-2 py-1 text-[9px] font-black uppercase text-emerald-500 ring-1 ring-emerald-500/20 transition-all"
                                    >Vinculado</span
                                >
                            </div>
                            <div
                                v-if="showDropdown && userResults.length"
                                class="absolute z-10 mt-2 w-full overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-2xl"
                            >
                                <button
                                    v-for="u in userResults"
                                    :key="u.id"
                                    type="button"
                                    @mousedown.prevent="selectUser(u)"
                                    class="group flex w-full items-center gap-3 border-b border-slate-50 dark:border-slate-700/50 px-6 py-4 text-left transition-colors last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700"
                                >
                                    <div
                                        :class="`flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black ${aCls(u.id)} transition-transform group-hover:scale-90`"
                                    >
                                        {{ initials(u.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold leading-tight text-slate-900 dark:text-slate-100">{{ u.name }}</p>
                                        <p class="truncate text-xs font-medium text-slate-400 dark:text-slate-400">{{ u.email }}</p>
                                    </div>
                                    <ChevronRight
                                        class="ml-auto h-4 w-4 text-slate-200 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100"
                                    />
                                </button>
                            </div>
                            <p v-if="createForm.errors.user_id" class="mt-2 text-[10px] font-bold uppercase tracking-widest text-rose-500">
                                {{ createForm.errors.user_id }}
                            </p>
                        </div>

                        <div v-if="productType === 'course'" class="space-y-2">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Curso *</label>
                            <AppSelect
                                v-model="createForm.course_id"
                                :options="[
                                    { value: '', label: '— Selecciona un curso —' },
                                    ...courses.map(c => ({ value: c.id, label: c.title }))
                                ]"
                                class="h-14 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 font-bold dark:text-slate-200"
                            />
                            <p v-if="createForm.errors.course_id" class="text-xs text-rose-500">{{ createForm.errors.course_id }}</p>
                        </div>

                        <div v-else-if="productType === 'book'" class="space-y-2">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Libro *</label>
                            <AppSelect
                                v-model="createForm.book_id"
                                :options="[
                                    { value: '', label: '— Selecciona un libro —' },
                                    ...books.map(b => ({ value: b.id, label: `${b.title} (S/ ${b.price})` }))
                                ]"
                                class="h-14 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 font-bold dark:text-slate-200"
                            />
                            <p v-if="createForm.errors.book_id" class="text-xs text-rose-500">{{ createForm.errors.book_id }}</p>
                        </div>

                        <div v-else class="space-y-2">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Plan de Membresía *</label>
                            <AppSelect
                                v-model="createForm.subscription_type"
                                :options="[
                                    { value: '', label: '— Selecciona un plan —' },
                                    ...(planOptions || []).map(p => ({ value: p.slug, label: `${p.name} (S/ ${p.price})` }))
                                ]"
                                class="h-14 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 font-bold dark:text-slate-200"
                            />
                            <p v-if="createForm.errors.subscription_type" class="text-xs text-rose-500">{{ createForm.errors.subscription_type }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Monto Final (S/) *</label>
                                <input
                                    v-model="createForm.amount"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="h-14 w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 px-4 text-sm font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-primary"
                                />
                                <p v-if="createForm.errors.amount" class="text-xs text-rose-500">{{ createForm.errors.amount }}</p>
                            </div>
                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estado Inicial</label>
                                <AppSelect
                                    v-model="createForm.status"
                                    :options="[
                                        { value: 'pendiente', label: '⏳ Pendiente' },
                                        { value: 'en_revision', label: '🔍 En revisión' },
                                        { value: 'aprobado', label: '✅ Aprobado' }
                                    ]"
                                    class="h-14 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 font-bold dark:text-slate-200"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                >Cargar Evidencia (Opcional)</label
                            >
                            <label
                                class="group flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800/50 p-5 transition-colors hover:border-primary/30"
                            >
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 transition-transform duration-500 group-hover:scale-110"
                                    >
                                        <FileImage class="h-5 w-5 text-slate-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="max-w-[200px] truncate text-sm font-bold text-slate-700 dark:text-slate-200">
                                            {{ createForm.comprobante ? createForm.comprobante.name : 'Vincular Comprobante' }}
                                        </p>
                                        <p class="text-[10px] font-medium uppercase tracking-widest text-slate-400">PNG, JPG o WEBP (Máx 5MB)</p>
                                    </div>
                                </div>
                                <input type="file" accept="image/*" class="hidden" @change="onFileChange" />
                            </label>
                            <img
                                v-if="createForm.comprobante"
                                :src="createPreviewUrl(createForm.comprobante)"
                                class="mt-4 max-h-32 w-full rounded-2xl border border-slate-100 bg-slate-50 object-contain"
                            />
                        </div>
                    </div>

                    <div class="flex flex-shrink-0 justify-end gap-3 border-t border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 p-8">
                        <button
                            type="button"
                            @click="handleClose"
                            class="h-12 rounded-2xl px-6 text-xs font-black uppercase tracking-widest text-slate-400 transition-all hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-600 dark:hover:text-slate-300"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="submitCreate"
                            :disabled="createForm.processing || !canSubmit()"
                            class="h-12 rounded-2xl bg-primary px-10 text-sm font-black text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                        >
                            {{ createForm.processing ? 'Procesando...' : 'Registrar Pago' }}
                        </button>
                    </div>
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
</style>
