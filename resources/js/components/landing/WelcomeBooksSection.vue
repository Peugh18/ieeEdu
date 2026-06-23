<script setup lang="ts">
import { useWelcomeCarousels } from '@/composables/useWelcomeCarousels';
import type { TeaserBook } from '@/types/public';
import { computed, ref } from 'vue';

const props = defineProps<{
    books: TeaserBook[];
}>();

const publicationTabs = [
    { id: 'free', label: 'Gratuitos' },
    { id: 'paid', label: 'Premium' },
];

const activePublicationTab = ref('free');

const filterFn = (book: TeaserBook, filter: string): boolean => {
    if (filter === 'free') {
        return !book.price || Number(book.price) === 0;
    }
    return Boolean(book.price && Number(book.price) > 0);
};

const {
    currentIndex,
    isMobile,
    scrollContainer,
    filteredItems: filteredPublications,
    goTo: goToBook,
    next: nextBook,
    prev: prevBook,
    scroll: scrollBooks,
} = useWelcomeCarousels(
    computed(() => props.books),
    activePublicationTab,
    filterFn,
);

const freeCount = computed(() => props.books.filter((b) => !b.price || Number(b.price) === 0).length);
const paidCount = computed(() => props.books.filter((b) => b.price && Number(b.price) > 0).length);
</script>

<template>
    <section id="publicaciones" class="reveal relative overflow-hidden bg-surface py-20 md:py-32">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-0 top-1/3 h-[500px] w-[500px] rounded-full bg-primary/[0.03] blur-[120px]"></div>
            <div class="absolute bottom-0 right-1/4 h-[400px] w-[400px] rounded-full bg-tertiary-container/[0.06] blur-[100px]"></div>
        </div>
        <div class="relative z-10 mx-auto max-w-7xl px-6 md:px-8">
            <div class="mb-6 flex items-center gap-3">
                <span class="font-serif text-[11px] font-bold uppercase tracking-[0.25em] text-primary/50">03</span>
                <div class="h-px w-8 bg-primary/30"></div>
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Publicaciones</span>
            </div>

            <div class="mb-12 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h2 class="mb-3 font-serif text-3xl leading-tight text-on-surface md:text-5xl">
                        Biblioteca de <span class="italic text-primary">Libros</span>
                    </h2>
                    <p class="max-w-lg text-base leading-relaxed text-on-surface-variant">
                        Recursos digitales gestionados por nuestro equipo editorial — gratuitos y premium.
                    </p>
                </div>
                <a
                    href="/publicaciones"
                    class="group inline-flex flex-shrink-0 items-center gap-2 rounded-xl border border-outline-variant/20 bg-surface-container px-6 py-3 text-sm font-bold text-on-surface transition-all duration-300 hover:border-primary/30 hover:bg-primary/5 hover:text-primary"
                >
                    Ver toda la biblioteca
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="mb-10 inline-flex items-center gap-1 rounded-2xl border border-outline-variant/15 bg-surface-container p-1">
                <button
                    v-for="tab in publicationTabs"
                    :key="tab.id"
                    @click.prevent="activePublicationTab = tab.id"
                    :class="[
                        'inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200',
                        activePublicationTab === tab.id ? 'bg-primary text-on-primary shadow-md' : 'text-on-surface-variant hover:text-on-surface',
                    ]"
                >
                    <svg v-if="tab.id === 'free'" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
                        />
                    </svg>
                    {{ tab.label }}
                    <span
                        :class="[
                            'rounded-full px-1.5 py-0.5 text-[10px] font-bold',
                            activePublicationTab === tab.id ? 'bg-white/20 text-white' : 'bg-outline-variant/20 text-on-surface-variant',
                        ]"
                    >
                        {{ tab.id === 'free' ? freeCount : paidCount }}
                    </span>
                </button>
            </div>

            <div v-if="isMobile && filteredPublications.length > 0" class="md:hidden">
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out" :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                        <div v-for="book in filteredPublications" :key="book.id" class="w-full flex-shrink-0 px-2">
                            <div
                                class="group relative flex flex-col overflow-hidden rounded-2xl border border-outline-variant/15 bg-surface-container"
                            >
                                <div class="relative h-52 flex-shrink-0 overflow-hidden bg-gradient-to-br from-primary/10 to-tertiary-container/20">
                                    <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="h-full w-full object-cover" />
                                    <div v-else class="absolute inset-0 flex items-center justify-center">
                                        <svg class="h-16 w-16 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                            />
                                        </svg>
                                    </div>
                                    <div class="absolute left-3 top-3">
                                        <span
                                            :class="[
                                                'rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest backdrop-blur-sm',
                                                !book.price || Number(book.price) === 0
                                                    ? 'bg-green-500/90 text-white'
                                                    : 'bg-primary/90 text-on-primary',
                                            ]"
                                        >
                                            {{ !book.price || Number(book.price) === 0 ? 'Gratis' : 'Premium' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <p v-if="book.category" class="mb-1.5 text-[10px] font-bold uppercase tracking-widest text-primary">
                                        {{ book.category }}
                                    </p>
                                    <h3 class="mb-1 font-serif text-base font-bold leading-snug text-on-surface">{{ book.title }}</h3>
                                    <p v-if="book.author" class="mb-4 text-sm text-on-surface-variant/60">{{ book.author }}</p>
                                    <div class="mt-auto flex items-center justify-between border-t border-outline-variant/10 pt-3">
                                        <span class="font-serif text-lg font-bold text-primary">
                                            {{ !book.price || Number(book.price) === 0 ? 'Gratis' : `S/ ${Number(book.price).toFixed(0)}` }}
                                        </span>
                                        <a
                                            v-if="Number(book.price) > 0"
                                            href="/publicaciones"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-on-primary"
                                        >
                                            Ver más
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                        <a
                                            v-else-if="book.download_url || book.file_path"
                                            :href="book.download_url || `/storage/${book.file_path}`"
                                            target="_blank"
                                            download
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-on-primary"
                                        >
                                            Descargar
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                                />
                                            </svg>
                                        </a>
                                        <button
                                            v-else
                                            disabled
                                            class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-xl bg-surface-container-highest px-4 py-2 text-xs font-bold text-on-surface-variant/40"
                                        >
                                            No disp.
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button
                        v-if="filteredPublications.length > 1"
                        @click="prevBook"
                        class="absolute left-2 top-1/3 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface shadow-md transition-all hover:bg-primary hover:text-on-primary"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        v-if="filteredPublications.length > 1"
                        @click="nextBook"
                        class="absolute right-2 top-1/3 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface shadow-md transition-all hover:bg-primary hover:text-on-primary"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="filteredPublications.length > 1" class="mt-6 flex justify-center gap-2">
                    <button
                        v-for="(_, index) in filteredPublications"
                        :key="index"
                        @click="goToBook(index)"
                        :class="[
                            'h-2.5 w-2.5 rounded-full transition-all duration-300',
                            currentIndex === index ? 'w-6 bg-primary' : 'bg-outline-variant/50 hover:bg-outline-variant',
                        ]"
                    ></button>
                </div>
            </div>

            <div v-if="filteredPublications.length > 0" class="group relative hidden md:block">
                <div ref="scrollContainer" class="hide-scrollbar flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-6">
                    <div
                        v-for="book in filteredPublications"
                        :key="book.id"
                        class="flex w-[calc(50%-1.25rem)] min-w-[240px] flex-shrink-0 snap-start lg:w-[calc(25%-1.25rem)]"
                    >
                        <div
                            class="group relative flex w-full flex-col overflow-hidden rounded-2xl border border-outline-variant/15 bg-surface-container transition-all duration-300 hover:-translate-y-1 hover:border-primary/20 hover:shadow-2xl"
                        >
                            <div class="relative h-44 flex-shrink-0 overflow-hidden bg-gradient-to-br from-primary/10 to-tertiary-container/20">
                                <img
                                    v-if="book.cover_image"
                                    :src="book.cover_image"
                                    :alt="book.title"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div v-else class="absolute inset-0 flex items-center justify-center">
                                    <svg class="h-16 w-16 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                        />
                                    </svg>
                                </div>
                                <div class="absolute left-3 top-3">
                                    <span
                                        :class="[
                                            'rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest backdrop-blur-sm',
                                            !book.price || Number(book.price) === 0 ? 'bg-green-500/90 text-white' : 'bg-primary/90 text-on-primary',
                                        ]"
                                    >
                                        {{ !book.price || Number(book.price) === 0 ? 'Gratis' : 'Premium' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <p v-if="book.category" class="mb-1.5 text-[10px] font-bold uppercase tracking-widest text-primary">
                                    {{ book.category }}
                                </p>
                                <h3 class="mb-1 line-clamp-2 flex-1 font-serif text-sm font-bold leading-snug text-on-surface">{{ book.title }}</h3>
                                <p v-if="book.author" class="mb-4 text-xs text-on-surface-variant/60">{{ book.author }}</p>
                                <div class="mt-auto flex items-center justify-between border-t border-outline-variant/10 pt-3">
                                    <span class="font-serif text-lg font-bold text-primary">
                                        {{ !book.price || Number(book.price) === 0 ? 'Gratis' : `S/ ${Number(book.price).toFixed(0)}` }}
                                    </span>
                                    <a
                                        v-if="Number(book.price) > 0"
                                        href="/publicaciones"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-on-primary transition-all hover:opacity-90 hover:shadow-md active:scale-95"
                                    >
                                        Ver más
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                    <a
                                        v-else-if="book.download_url || book.file_path"
                                        :href="book.download_url || `/storage/${book.file_path}`"
                                        target="_blank"
                                        download
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-on-primary transition-all hover:opacity-90 hover:shadow-md active:scale-95"
                                    >
                                        Descargar
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                            />
                                        </svg>
                                    </a>
                                    <button
                                        v-else
                                        disabled
                                        class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-xl bg-surface-container-highest px-4 py-2 text-xs font-bold text-on-surface-variant/40"
                                    >
                                        No disp.
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button
                    v-if="filteredPublications.length > 4"
                    @click="scrollBooks('left')"
                    class="absolute -left-5 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface opacity-0 shadow-lg transition-all hover:border-primary hover:bg-primary hover:text-on-primary group-hover:opacity-100"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    v-if="filteredPublications.length > 4"
                    @click="scrollBooks('right')"
                    class="absolute -right-5 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface opacity-0 shadow-lg transition-all hover:border-primary hover:bg-primary hover:text-on-primary group-hover:opacity-100"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <div v-else class="py-24 text-center">
                <div
                    class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl border border-outline-variant/15 bg-surface-container"
                >
                    <svg class="h-10 w-10 text-on-surface-variant/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                </div>
                <p class="mb-1 font-serif text-lg font-bold text-on-surface-variant">Sin publicaciones aún</p>
                <p class="text-sm text-on-surface-variant/50">El equipo editorial pronto añadirá libros en esta categoría.</p>
            </div>
        </div>
    </section>
</template>

<style scoped>
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
</style>
