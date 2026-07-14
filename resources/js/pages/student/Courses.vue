<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BookOpen, CheckCircle, LayoutGrid, List, PlayCircle, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Course {
    id: number;
    title: string;
    slug: string;
    image: string;
    type: string;
    category: { name: string };
    pivot: {
        enrolled_at: string;
        completed_at: string | null;
        progress?: number;
    };
}

const props = defineProps<{
    courses: Course[];
}>();

const currentFilter = ref('all');
const searchTerm = ref('');
const viewMode = ref<'grid' | 'list'>('grid');

const filters = [
    { id: 'all', label: 'Todo mi catálogo', icon: BookOpen },
    { id: 'active', label: 'En curso', icon: PlayCircle },
    { id: 'completed', label: 'Finalizados', icon: CheckCircle },
];

const filteredCourses = computed(() => {
    let result = props.courses;
    if (currentFilter.value === 'active') {
        result = result.filter((c) => !c.pivot?.completed_at && (c.pivot?.progress ?? 0) < 100);
    } else if (currentFilter.value === 'completed') {
        result = result.filter((c) => !!c.pivot?.completed_at || (c.pivot?.progress ?? 0) >= 100);
    }
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        result = result.filter((c) => c.title.toLowerCase().includes(term));
    }
    return result;
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Cursos Académicos', href: '/student/courses' },
];

const currentPage = ref(1);

const pageSize = computed(() => {
    return viewMode.value === 'grid' ? 6 : 10;
});

watch([currentFilter, searchTerm, viewMode], () => {
    currentPage.value = 1;
});

const totalPages = computed(() => {
    return Math.ceil(filteredCourses.value.length / pageSize.value);
});

const paginatedCourses = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filteredCourses.value.slice(start, end);
});

function scrollToTop() {
    const headerElement = document.querySelector('header');
    if (headerElement) {
        headerElement.scrollIntoView({ behavior: 'smooth' });
    } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
</script>

<template>
    <Head title="Mis Cursos Académicos - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background">
            <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 md:space-y-8 md:px-8 md:py-8 pb-24">
                <!-- Clean Modern Header -->
                <header class="mb-6 flex flex-col justify-between gap-6 md:flex-row md:items-start">
                    <div class="flex-1">
                        <div class="mb-2 flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                                <BookOpen class="h-4 w-4 text-primary" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-primary">Expediente Académico</span>
                        </div>
                        <h1 class="text-2xl font-bold text-on-background md:text-3xl">Mis Cursos</h1>
                        <p class="mt-1 max-w-2xl text-sm text-on-surface-variant/70">
                            Gestiona tu progreso y accede a programas diseñados para alcanzar la excelencia profesional.
                        </p>
                    </div>
                    
                    <!-- Integrated Search -->
                    <div class="w-full md:w-80 shrink-0">
                        <div class="group relative">
                            <Search class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-outline-variant transition-colors group-focus-within:text-primary" />
                            <input
                                v-model="searchTerm"
                                type="text"
                                placeholder="Buscar en mi catálogo..."
                                class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest py-2.5 pl-10 pr-4 text-sm text-on-background transition-all placeholder:text-on-surface-variant/50 focus:border-primary/40 focus:ring-4 focus:ring-primary/10"
                            />
                        </div>
                    </div>
                </header>

                <!-- Refined Filter Tabs -->
                <div
                    class="flex w-full flex-col items-center justify-between gap-4 rounded-2xl border border-white bg-white/50 p-3 shadow-sm backdrop-blur-sm dark:border-slate-800 dark:bg-slate-900/50 md:rounded-[2.5rem] md:p-4 lg:flex-row"
                >
                    <div class="grid w-full grid-cols-3 gap-2 lg:flex lg:flex-row lg:gap-3">
                        <button
                            v-for="filter in filters"
                            :key="filter.id"
                            @click="currentFilter = filter.id"
                            class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl px-3 py-3 text-[9px] font-black uppercase tracking-wider transition-all duration-500 md:rounded-2xl md:px-6 md:py-3.5 md:text-[11px] md:tracking-[0.15em] lg:w-auto"
                            :class="
                                currentFilter === filter.id
                                    ? 'scale-105 bg-primary text-white shadow-xl shadow-primary/20'
                                    : 'border border-outline-variant/20 bg-transparent text-on-surface-variant hover:bg-surface-container-highest dark:border-slate-800 dark:hover:bg-slate-800'
                            "
                        >
                            <component
                                :is="filter.icon"
                                class="h-3.5 w-3.5 shrink-0"
                                :class="currentFilter === filter.id ? 'text-[#D4AF37]' : 'text-primary/40'"
                            />
                            <span>
                                <span v-if="filter.id === 'all'">
                                    <span class="md:hidden">Todo</span>
                                    <span class="hidden md:inline">Todo mi catálogo</span>
                                </span>
                                <span v-else>{{ filter.label }}</span>
                            </span>
                        </button>
                    </div>

                    <div class="flex w-full shrink-0 items-center justify-between gap-6 lg:w-auto lg:justify-end">
                        <div
                            class="flex items-center gap-2 whitespace-nowrap text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/40 md:text-[11px]"
                        >
                            <span class="hidden h-[1px] w-8 bg-outline-variant md:inline-block"></span>
                            Exhibiendo {{ filteredCourses.length }} programas
                        </div>

                        <!-- Grid/List Toggle -->
                        <div class="flex items-center rounded-xl border border-slate-200/20 bg-slate-100 p-1 dark:bg-slate-800/50">
                            <button
                                @click="viewMode = 'grid'"
                                class="rounded-lg p-2 transition-all"
                                :class="
                                    viewMode === 'grid'
                                        ? 'bg-white text-primary shadow-sm dark:bg-slate-900'
                                        : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'
                                "
                            >
                                <LayoutGrid class="h-4 w-4" />
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                class="rounded-lg p-2 transition-all"
                                :class="
                                    viewMode === 'list'
                                        ? 'bg-white text-primary shadow-sm dark:bg-slate-900'
                                        : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'
                                "
                            >
                                <List class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Academic Catalog Content -->
                <div v-if="filteredCourses.length > 0">
                    <!-- Grid View -->
                    <div v-if="viewMode === 'grid'" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="course in paginatedCourses"
                            :key="course.id"
                            class="group flex flex-col overflow-hidden rounded-3xl border border-outline-variant/15 bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-slate-800/80 dark:bg-slate-900"
                        >
                            <!-- Visual Cover -->
                            <div class="relative aspect-video overflow-hidden bg-surface-container-highest">
                                <img
                                    v-if="course.image"
                                    :src="course.image"
                                    :alt="course.title"
                                    class="h-full w-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-105"
                                />
                                <!-- Overlay gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-60"></div>

                                <div class="absolute left-4 top-4">
                                    <span
                                        class="rounded border border-white/10 bg-black/35 px-3 py-1.5 text-[9px] font-bold uppercase tracking-widest text-white shadow-sm backdrop-blur-md"
                                    >
                                        {{ course.type }}
                                    </span>
                                </div>
                            </div>

                            <!-- Academic Content -->
                            <div class="flex flex-1 flex-col justify-between p-5 md:p-6">
                                <div class="space-y-3">
                                    <div
                                        class="flex items-center gap-3 text-[9px] font-bold uppercase tracking-widest text-primary/70 dark:text-primary-fixed-dim/80"
                                    >
                                        {{ course.category?.name }}
                                    </div>
                                    <h3
                                        class="line-clamp-2 font-serif text-lg font-bold leading-tight text-on-background transition-colors hover:text-primary dark:text-on-surface md:text-xl"
                                        :title="course.title"
                                    >
                                        {{ course.title }}
                                    </h3>
                                </div>

                                <div class="mt-4 space-y-3.5 border-t border-slate-100 pt-4 dark:border-slate-800/60">
                                    <!-- Progress Bar -->
                                    <div class="space-y-1">
                                        <div
                                            class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-on-surface-variant/60"
                                        >
                                            <span class="flex items-center gap-1.5 italic">
                                                <CheckCircle
                                                    v-if="course.pivot.completed_at || (course.pivot.progress || 0) >= 100"
                                                    class="h-3.5 w-3.5 text-[#D4AF37]"
                                                />
                                                <PlayCircle v-else class="h-3.5 w-3.5 text-primary" />
                                                {{
                                                    course.pivot.completed_at || (course.pivot.progress || 0) >= 100
                                                        ? 'Módulo Completado'
                                                        : 'Sesiones en curso'
                                                }}
                                            </span>
                                            <span>{{ course.pivot.progress || (course.pivot.completed_at ? 100 : 0) }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                            <div
                                                class="h-full rounded-full bg-[#D4AF37] transition-all duration-500"
                                                :style="{ width: `${course.pivot.progress || (course.pivot.completed_at ? 100 : 0)}%` }"
                                            ></div>
                                        </div>
                                    </div>

                                    <Link
                                        :href="route('student.classroom', { course: course.slug })"
                                        class="group/btn relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-primary py-3.5 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-sm transition-all duration-300 hover:bg-on-background"
                                    >
                                        <span class="relative z-10">{{
                                            course.pivot.completed_at || (course.pivot.progress || 0) >= 100 ? 'Repasar Lecciones' : 'Aula Virtual'
                                        }}</span>
                                        <ArrowRight class="relative z-10 h-3.5 w-3.5 transition-transform group-hover/btn:translate-x-1" />
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- List View (Responsive) -->
                    <div v-else class="duration-500 animate-in fade-in slide-in-from-bottom-4">
                        <!-- Mobile List (Stacked Cards) -->
                        <div class="space-y-4 md:hidden">
                            <div
                                v-for="course in paginatedCourses"
                                :key="course.id"
                                class="space-y-4 rounded-2xl border border-outline-variant/15 bg-white p-5 shadow-sm dark:border-slate-800/80 dark:bg-slate-900"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-slate-200/50 bg-slate-50 dark:border-slate-700/50 dark:bg-slate-800"
                                    >
                                        <img v-if="course.image" :src="course.image" :alt="course.title" class="h-full w-full object-cover" />
                                    </div>
                                    <div class="flex min-w-0 flex-col">
                                        <span class="mb-0.5 text-[9px] font-bold uppercase tracking-widest text-[#D4AF37]">{{
                                            course.category?.name
                                        }}</span>
                                        <h4
                                            class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 dark:text-slate-100"
                                            :title="course.title"
                                        >
                                            {{ course.title }}
                                        </h4>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-3 dark:border-slate-800/60">
                                    <span
                                        class="inline-flex items-center rounded border border-slate-200/60 bg-slate-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-400"
                                    >
                                        {{ course.type }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold"
                                        :class="
                                            course.pivot.completed_at
                                                ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400'
                                                : 'bg-blue-50 text-blue-600 dark:bg-blue-950/20 dark:text-blue-400'
                                        "
                                    >
                                        <CheckCircle v-if="course.pivot.completed_at" class="h-3.5 w-3.5" />
                                        <PlayCircle v-else class="h-3.5 w-3.5" />
                                        {{ course.pivot.completed_at ? 'Completado' : 'En Curso' }}
                                    </span>
                                </div>

                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400">
                                        <span>Progreso</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200"
                                            >{{ course.pivot.progress || (course.pivot.completed_at ? 100 : 0) }}%</span
                                        >
                                    </div>
                                    <div class="h-2 w-full overflow-hidden rounded-full border border-slate-200/20 bg-slate-100 dark:bg-slate-800">
                                        <div
                                            class="h-full rounded-full bg-[#D4AF37] transition-all duration-500"
                                            :style="{ width: `${course.pivot.progress || (course.pivot.completed_at ? 100 : 0)}%` }"
                                        ></div>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <Link
                                        :href="route('student.classroom', { course: course.slug })"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-3.5 text-[11px] font-black uppercase tracking-[0.2em] text-white transition-all duration-300 hover:bg-on-background"
                                    >
                                        <span>{{ course.pivot.completed_at ? 'Repasar Lecciones' : 'Aula Virtual' }}</span>
                                        <ArrowRight class="h-4 w-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop List (Table Layout) -->
                        <div
                            class="hidden overflow-hidden rounded-[2.5rem] border border-outline-variant/20 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 md:block"
                        >
                            <div class="custom-scrollbar overflow-x-auto">
                                <table class="w-full min-w-[800px] border-collapse text-left">
                                    <thead class="border-b border-slate-100 bg-slate-50/80 dark:border-slate-800 dark:bg-slate-800/80">
                                        <tr>
                                            <th
                                                class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                            >
                                                Programa Académico
                                            </th>
                                            <th
                                                class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                            >
                                                Tipo
                                            </th>
                                            <th
                                                class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                            >
                                                Progreso
                                            </th>
                                            <th
                                                class="px-6 py-5 text-center text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                            >
                                                Estado
                                            </th>
                                            <th
                                                class="px-8 py-5 text-right text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500"
                                            >
                                                Acceso
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                                        <tr
                                            v-for="course in paginatedCourses"
                                            :key="course.id"
                                            class="group transition-all duration-300 hover:bg-slate-50/50 dark:hover:bg-slate-800/50"
                                        >
                                            <td class="relative px-8 py-5">
                                                <div
                                                    class="absolute left-0 top-1/2 h-0 w-1 -translate-y-1/2 rounded-r-full bg-primary transition-all duration-500 group-hover:h-12"
                                                ></div>
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="relative h-10 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-slate-200/50 bg-slate-50 shadow-inner dark:border-slate-700/50 dark:bg-slate-800"
                                                    >
                                                        <img
                                                            v-if="course.image"
                                                            :src="course.image"
                                                            :alt="course.title"
                                                            class="h-full w-full object-cover"
                                                        />
                                                    </div>
                                                    <div class="flex min-w-0 flex-col">
                                                        <span class="mb-1 text-[9px] font-bold uppercase tracking-widest text-[#D4AF37]">{{
                                                            course.category?.name
                                                        }}</span>
                                                        <h4
                                                            class="line-clamp-1 text-sm font-bold leading-tight text-slate-900 transition-colors group-hover:text-primary dark:text-slate-100"
                                                            :title="course.title"
                                                        >
                                                            {{ course.title }}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span
                                                    class="inline-flex items-center rounded-md border border-slate-200/60 bg-slate-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-400"
                                                >
                                                    {{ course.type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-5">
                                                <div class="flex max-w-[200px] items-center gap-3">
                                                    <div
                                                        class="h-2 w-full overflow-hidden rounded-full border border-slate-200/20 bg-slate-100 dark:bg-slate-800"
                                                    >
                                                        <div
                                                            class="h-full rounded-full bg-[#D4AF37] transition-all duration-500"
                                                            :style="{ width: `${course.pivot.progress || (course.pivot.completed_at ? 100 : 0)}%` }"
                                                        ></div>
                                                    </div>
                                                    <span class="text-xs font-bold tabular-nums text-slate-600 dark:text-slate-400">
                                                        {{ course.pivot.progress || (course.pivot.completed_at ? '100' : '0') }}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold"
                                                    :class="
                                                        course.pivot.completed_at
                                                            ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400'
                                                            : 'bg-blue-50 text-blue-600 dark:bg-blue-950/20 dark:text-blue-400'
                                                    "
                                                >
                                                    <CheckCircle v-if="course.pivot.completed_at" class="h-3.5 w-3.5" />
                                                    <PlayCircle v-else class="h-3.5 w-3.5" />
                                                    {{ course.pivot.completed_at ? 'Completado' : 'En Curso' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div class="flex items-center justify-end">
                                                    <Link
                                                        :href="route('student.classroom', { course: course.slug })"
                                                        class="flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-[10px] font-black uppercase tracking-[0.15em] text-white shadow-md shadow-primary/15 transition-all duration-300 hover:bg-on-background active:scale-95"
                                                    >
                                                        <span>{{ course.pivot.completed_at ? 'Repasar' : 'Aula' }}</span>
                                                        <ArrowRight class="h-3 w-3" />
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Client-side Pagination Controls -->
                    <div
                        v-if="totalPages > 1"
                        class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-slate-100 pt-6 dark:border-slate-800/80 sm:flex-row"
                    >
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            Exhibiendo del {{ (currentPage - 1) * pageSize + 1 }} al {{ Math.min(currentPage * pageSize, filteredCourses.length) }} de
                            {{ filteredCourses.length }} cursos
                        </span>

                        <div class="bg-slate-105 flex items-center gap-1 rounded-xl border border-slate-200/20 p-1 shadow-inner dark:bg-slate-800/50">
                            <!-- Prev Button -->
                            <button
                                @click="currentPage > 1 && (currentPage--, scrollToTop())"
                                :disabled="currentPage === 1"
                                class="flex select-none items-center gap-1 rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.1em] transition-all"
                                :class="
                                    currentPage === 1
                                        ? 'cursor-not-allowed text-slate-300 opacity-50 dark:text-slate-600'
                                        : 'dark:text-slate-350 text-slate-700 shadow-sm hover:bg-white hover:text-primary dark:hover:bg-slate-900'
                                "
                            >
                                Anterior
                            </button>

                            <!-- Page Numbers -->
                            <button
                                v-for="page in totalPages"
                                :key="page"
                                @click="
                                    currentPage = page;
                                    scrollToTop();
                                "
                                class="h-8 w-8 rounded-lg text-xs font-bold transition-all"
                                :class="
                                    currentPage === page
                                        ? 'scale-105 bg-primary text-white shadow-md shadow-primary/20'
                                        : 'text-slate-600 hover:bg-white hover:text-primary dark:text-slate-400 dark:hover:bg-slate-900'
                                "
                            >
                                {{ page }}
                            </button>

                            <!-- Next Button -->
                            <button
                                @click="currentPage < totalPages && (currentPage++, scrollToTop())"
                                :disabled="currentPage === totalPages"
                                class="flex select-none items-center gap-1 rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.1em] transition-all"
                                :class="
                                    currentPage === totalPages
                                        ? 'cursor-not-allowed text-slate-300 opacity-50 dark:text-slate-600'
                                        : 'dark:text-slate-350 text-slate-700 shadow-sm hover:bg-white hover:text-primary dark:hover:bg-slate-900'
                                "
                            >
                                Siguiente
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Academic Empty State -->
                <div
                    v-else
                    class="relative mx-auto max-w-4xl space-y-10 overflow-hidden rounded-[4rem] border border-outline-variant/20 bg-white py-32 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900"
                >
                    <!-- Decorative background -->
                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-[0.03]">
                        <BookOpen class="h-[500px] w-[500px]" />
                    </div>

                    <div class="relative z-10 space-y-8 px-8">
                        <div
                            class="mb-4 inline-flex rounded-[3rem] border border-outline-variant/30 bg-background p-10 shadow-inner dark:border-slate-700/50 dark:bg-slate-800"
                        >
                            <BookOpen class="h-16 w-16 text-outline-variant dark:text-slate-500" />
                        </div>
                        <div class="mx-auto max-w-lg space-y-4">
                            <h3 class="font-serif text-4xl font-bold italic text-on-background">Su registro académico está vacío</h3>
                            <p class="font-serif text-lg italic leading-relaxed text-on-surface-variant">
                                Aún no se ha inscrito en programas académicos o sus filtros no arrojan resultados en su expediente actual.
                            </p>
                        </div>
                        <Link
                            :href="route('cursos.index')"
                            class="group inline-flex items-center gap-4 text-xs font-black uppercase tracking-[0.25em] text-primary transition-all hover:text-[#D4AF37]"
                        >
                            Explorar Catálogo de Especialización
                            <span class="h-[1px] w-12 bg-outline-variant transition-all group-hover:w-16 group-hover:bg-[#D4AF37]"></span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <BottomNav active="courses" />
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(87, 87, 42, 0.08);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(87, 87, 42, 0.15);
}
</style>
