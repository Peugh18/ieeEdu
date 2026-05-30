<script setup lang="ts">
import { ref, computed } from 'vue';
import { useWelcomeCarousels } from '@/composables/useWelcomeCarousels';
import type { TeaserBook } from '@/types/public';

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
  filterFn
);

const freeCount = computed(() => props.books.filter(b => !b.price || Number(b.price) === 0).length);
const paidCount = computed(() => props.books.filter(b => b.price && Number(b.price) > 0).length);
</script>

<template>
  <section id="publicaciones" class="py-20 md:py-32 bg-surface relative overflow-hidden reveal">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
      <div class="absolute top-1/3 left-0 w-[500px] h-[500px] bg-primary/[0.03] rounded-full blur-[120px]"></div>
      <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.06] rounded-full blur-[100px]"></div>
    </div>
    <div class="max-w-7xl mx-auto px-6 md:px-8 relative z-10">

      <div class="flex items-center gap-3 mb-6">
        <span class="text-[11px] font-bold text-primary/50 tracking-[0.25em] uppercase font-serif">03</span>
        <div class="w-8 h-px bg-primary/30"></div>
        <span class="text-[11px] font-bold text-primary tracking-[0.2em] uppercase">Publicaciones</span>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
        <div>
          <h2 class="font-serif text-3xl md:text-5xl text-on-surface leading-tight mb-3">
            Biblioteca de <span class="italic text-primary">Libros</span>
          </h2>
          <p class="text-on-surface-variant text-base max-w-lg leading-relaxed">
            Recursos digitales gestionados por nuestro equipo editorial — gratuitos y premium.
          </p>
        </div>
        <a href="/publicaciones" class="group flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-surface-container border border-outline-variant/20 text-on-surface font-bold text-sm hover:border-primary/30 hover:text-primary hover:bg-primary/5 transition-all duration-300">
          Ver toda la biblioteca
          <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      </div>

      <div class="inline-flex items-center p-1 rounded-2xl bg-surface-container border border-outline-variant/15 mb-10 gap-1">
        <button
          v-for="tab in publicationTabs"
          :key="tab.id"
          @click.prevent="activePublicationTab = tab.id"
          :class="[
            'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200',
            activePublicationTab === tab.id
              ? 'bg-primary text-on-primary shadow-md'
              : 'text-on-surface-variant hover:text-on-surface'
          ]"
        >
          <svg v-if="tab.id === 'free'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
          {{ tab.label }}
          <span :class="['text-[10px] font-bold px-1.5 py-0.5 rounded-full', activePublicationTab === tab.id ? 'bg-white/20 text-white' : 'bg-outline-variant/20 text-on-surface-variant']">
            {{ tab.id === 'free' ? freeCount : paidCount }}
          </span>
        </button>
      </div>

      <div v-if="isMobile && filteredPublications.length > 0" class="md:hidden">
        <div class="relative overflow-hidden">
          <div class="flex transition-transform duration-500 ease-out"
               :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
            <div v-for="book in filteredPublications" :key="book.id" class="w-full flex-shrink-0 px-2">
              <div class="group relative bg-surface-container rounded-2xl border border-outline-variant/15 overflow-hidden flex flex-col">
                <div class="relative h-52 overflow-hidden bg-gradient-to-br from-primary/10 to-tertiary-container/20 flex-shrink-0">
                  <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover"/>
                  <div v-else class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-16 h-16 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                  </div>
                  <div class="absolute top-3 left-3">
                    <span :class="['text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full backdrop-blur-sm', !book.price || Number(book.price) === 0 ? 'bg-green-500/90 text-white' : 'bg-primary/90 text-on-primary']">
                      {{ !book.price || Number(book.price) === 0 ? 'Gratis' : 'Premium' }}
                    </span>
                  </div>
                </div>
                <div class="p-5 flex flex-col flex-1">
                  <p v-if="book.category" class="text-primary text-[10px] font-bold uppercase tracking-widest mb-1.5">{{ book.category }}</p>
                  <h3 class="font-serif text-base text-on-surface font-bold leading-snug mb-1">{{ book.title }}</h3>
                  <p v-if="book.author" class="text-on-surface-variant/60 text-sm mb-4">{{ book.author }}</p>
                  <div class="flex items-center justify-between mt-auto pt-3 border-t border-outline-variant/10">
                    <span class="text-lg font-bold text-primary font-serif">
                      {{ !book.price || Number(book.price) === 0 ? 'Gratis' : `S/ ${Number(book.price).toFixed(0)}` }}
                    </span>
                    <a v-if="Number(book.price) > 0" href="/publicaciones"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary text-xs font-bold">
                      Ver más
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a v-else-if="book.download_url || book.file_path" 
                       :href="book.download_url || `/storage/${book.file_path}`"
                       target="_blank" download
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary text-xs font-bold">
                      Descargar
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                    <button v-else disabled
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-surface-container-highest text-on-surface-variant/40 text-xs font-bold cursor-not-allowed">
                      No disp.
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button v-if="filteredPublications.length > 1" @click="prevBook"
                  class="absolute left-2 top-1/3 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-all z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button v-if="filteredPublications.length > 1" @click="nextBook"
                  class="absolute right-2 top-1/3 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-all z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>

        <div v-if="filteredPublications.length > 1" class="flex justify-center gap-2 mt-6">
          <button v-for="(_, index) in filteredPublications" :key="index"
                  @click="goToBook(index)"
                  :class="['w-2.5 h-2.5 rounded-full transition-all duration-300', currentIndex === index ? 'bg-primary w-6' : 'bg-outline-variant/50 hover:bg-outline-variant']">
          </button>
        </div>
      </div>

      <div v-if="filteredPublications.length > 0" class="hidden md:block relative group">
        <div ref="scrollContainer" class="flex overflow-x-auto snap-x snap-mandatory gap-5 pb-6 hide-scrollbar scroll-smooth">
          <div v-for="book in filteredPublications" :key="book.id" class="snap-start flex-shrink-0 w-[calc(50%-1.25rem)] lg:w-[calc(25%-1.25rem)] min-w-[240px] flex">
            <div class="group relative bg-surface-container rounded-2xl border border-outline-variant/15 overflow-hidden hover:shadow-2xl hover:border-primary/20 hover:-translate-y-1 transition-all duration-300 flex flex-col w-full">
              <div class="relative h-44 overflow-hidden bg-gradient-to-br from-primary/10 to-tertiary-container/20 flex-shrink-0">
                <img
                  v-if="book.cover_image"
                  :src="book.cover_image"
                  :alt="book.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
                <div v-else class="absolute inset-0 flex items-center justify-center">
                  <svg class="w-16 h-16 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="absolute top-3 left-3">
                  <span :class="['text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full backdrop-blur-sm', !book.price || Number(book.price) === 0 ? 'bg-green-500/90 text-white' : 'bg-primary/90 text-on-primary']">
                    {{ !book.price || Number(book.price) === 0 ? 'Gratis' : 'Premium' }}
                  </span>
                </div>
              </div>

              <div class="p-5 flex flex-col flex-1">
                <p v-if="book.category" class="text-primary text-[10px] font-bold uppercase tracking-widest mb-1.5">{{ book.category }}</p>
                <h3 class="font-serif text-sm text-on-surface font-bold leading-snug mb-1 flex-1 line-clamp-2">{{ book.title }}</h3>
                <p v-if="book.author" class="text-on-surface-variant/60 text-xs mb-4">{{ book.author }}</p>
                <div class="flex items-center justify-between mt-auto pt-3 border-t border-outline-variant/10">
                  <span class="text-lg font-bold text-primary font-serif">
                    {{ !book.price || Number(book.price) === 0 ? 'Gratis' : `S/ ${Number(book.price).toFixed(0)}` }}
                  </span>
                  <a v-if="Number(book.price) > 0" href="/publicaciones"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary text-xs font-bold hover:opacity-90 hover:shadow-md active:scale-95 transition-all">
                    Ver más
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                  </a>
                  <a v-else-if="book.download_url || book.file_path" 
                    :href="book.download_url || `/storage/${book.file_path}`"
                    target="_blank" download
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-on-primary text-xs font-bold hover:opacity-90 hover:shadow-md active:scale-95 transition-all">
                    Descargar
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                  </a>
                  <button v-else disabled
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-surface-container-highest text-on-surface-variant/40 text-xs font-bold cursor-not-allowed">
                    No disp.
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <button v-if="filteredPublications.length > 4" @click="scrollBooks('left')" class="absolute -left-5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-lg flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all opacity-0 group-hover:opacity-100 z-10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button v-if="filteredPublications.length > 4" @click="scrollBooks('right')" class="absolute -right-5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-lg flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all opacity-0 group-hover:opacity-100 z-10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>

      <div v-else class="py-24 text-center">
        <div class="w-20 h-20 rounded-3xl bg-surface-container mx-auto flex items-center justify-center mb-5 border border-outline-variant/15">
          <svg class="w-10 h-10 text-on-surface-variant/25" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p class="text-on-surface-variant font-serif text-lg font-bold mb-1">Sin publicaciones aún</p>
        <p class="text-on-surface-variant/50 text-sm">El equipo editorial pronto añadirá libros en esta categoría.</p>
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
