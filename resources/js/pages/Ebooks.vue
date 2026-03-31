<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';

interface Ebook {
  id: number;
  title: string;
  category: string;
  author: string;
  pages: number;
  size: string;
  price: number;
  isFree: boolean;
  cover: string;
}

const ebooks = ref<Ebook[]>([
  {
    id: 1,
    title: 'Lectura e Interpretación de Planos Estructurales',
    category: 'Construcción',
    author: 'IEE',
    pages: 145,
    size: '7.5 MB',
    price: 145,
    isFree: false,
    cover: '/images/landing/ebook-construccion.jpg',
  },
  {
    id: 2,
    title: 'Productividad y Gestión del Tiempo',
    category: 'Desarrollo Personal',
    author: 'Dra. Patricia Silva',
    pages: 108,
    size: '3.1 MB',
    price: 0,
    isFree: true,
    cover: '/images/landing/ebook-productividad.jpg',
  },
  {
    id: 3,
    title: 'Análisis Financiero para la Alta Dirección',
    category: 'Finanzas',
    author: 'Dr. Carlos Mendoza',
    pages: 178,
    size: '5.6 MB',
    price: 120,
    isFree: false,
    cover: '/images/landing/ebook-finanzas.jpg',
  },
  {
    id: 4,
    title: 'Habilidades de Negociación Estratégica',
    category: 'Negocios',
    author: 'Mg. Roberto Flores',
    pages: 132,
    size: '4.0 MB',
    price: 0,
    isFree: true,
    cover: '/images/landing/ebook-negocios.jpg',
  },
]);

const categories = ['Todas', 'Construcción', 'Desarrollo Personal', 'Marketing', 'Negocios', 'Tecnología', 'Finanzas'];
const selectedCategory = ref('Todas');
const sortBy = ref('Mas reciente');

const filteredEbooks = computed(() => {
  let list = ebooks.value;
  if (selectedCategory.value !== 'Todas') {
    list = list.filter((item) => item.category === selectedCategory.value);
  }

  if (sortBy.value === 'Menor precio') {
    list = [...list].sort((a, b) => a.price - b.price);
  } else if (sortBy.value === 'Mayor precio') {
    list = [...list].sort((a, b) => b.price - a.price);
  }

  return list;
});
</script>

<template>
  <Head title="IEE - Ebooks" />

  <div class="flex min-h-screen flex-col bg-[#FAFAF5] text-[#1A1C19] font-sans">
    <Navigation />

    <main class="flex-1 pb-20">
      <!-- Hero Header -->
      <section class="bg-gradient-to-br from-[#57572A] to-[#707040] py-20 px-6 sm:px-12 text-center relative mb-12">
        <div class="relative z-10 max-w-4xl mx-auto">
          <p class="text-[#FAFAF5]/80 text-sm font-bold uppercase tracking-[0.1em] mb-4">Publicaciones IEE</p>
          <h1 class="text-4xl md:text-5xl lg:text-[64px] font-serif font-bold text-white mb-6 leading-tight tracking-[-0.02em]">
            Biblioteca de Ebooks
          </h1>
          <p class="text-[#FAFAF5]/90 text-lg md:text-xl max-w-2xl mx-auto font-serif">
            Descarga contenidos gratuitos o adquiere libros premium para potenciar tu especialización profesional.
          </p>
        </div>
      </section>

      <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-10 p-6 bg-[#F4F4EF] rounded">
          <div class="flex flex-wrap gap-2">
            <button
              v-for="category in categories"
              :key="category"
              @click="selectedCategory = category"
              :class="[
                'px-4 py-2 rounded text-[11px] font-bold tracking-[0.05em] uppercase transition',
                selectedCategory === category 
                  ? 'bg-[#57572A] text-white' 
                  : 'bg-transparent text-[#57572A] hover:bg-[#EBEBE3] border border-[#57572A]/20'
              ]"
            >
              {{ category }}
            </button>
          </div>

          <div class="flex items-center gap-3">
            <span class="text-[#5F5E5E] text-xs uppercase tracking-widest font-bold">Ordenar por:</span>
            <div class="relative">
              <select v-model="sortBy" class="bg-white border border-[#C9C7B8] rounded px-4 py-2 text-sm text-[#1A1C19] appearance-none pr-8 focus:ring-0 focus:border-[#57572A]">
                <option>Mas reciente</option>
                <option>Menor precio</option>
                <option>Mayor precio</option>
              </select>
              <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-[#5F5E5E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
          </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <template v-if="filteredEbooks.length">
            <article
              v-for="item in filteredEbooks"
              :key="item.id"
              class="bg-white rounded border border-[#C9C7B8]/30 flex flex-col group overflow-hidden shadow-[0_20px_40px_rgba(26,28,25,0.04)]"
            >
              <div class="h-56 overflow-hidden bg-[#F4F4EF] relative">
                <!-- Using a placeholder pattern as fallback if image fails, and the actual image if exists -->
                <img :src="item.cover" :alt="item.title" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
                
                <div class="absolute top-4 left-4 z-10">
                  <span class="px-3 py-1.5 rounded bg-white/90 backdrop-blur-[20px] text-[#1A1C19] text-[10px] font-bold tracking-widest uppercase">
                    {{ item.category }}
                  </span>
                </div>
              </div>
              
              <div class="p-6 flex flex-col flex-1">
                <h2 class="font-serif text-lg font-bold text-[#1A1C19] mb-3 leading-snug group-hover:text-[#57572A] transition-colors line-clamp-2" :title="item.title">
                  {{ item.title }}
                </h2>
                <div class="text-[13px] text-[#5F5E5E] mb-6 space-y-1">
                  <p class="font-bold">Autor: {{ item.author }}</p>
                  <p>{{ item.pages }} páginas <span class="mx-1">•</span> {{ item.size }}</p>
                </div>

                <div class="mt-auto pt-6 border-t border-[#C9C7B8]/30 flex items-end justify-between">
                  <div class="text-2xl font-serif font-bold text-[#1A1C19]">
                    {{ item.isFree ? 'Gratis' : `S/ ${item.price}` }}
                  </div>
                  <Link
                    href="#"
                    class="rounded text-[11px] uppercase tracking-[0.05em] font-bold px-4 py-2.5 bg-gradient-to-br from-[#57572A] to-[#707040] text-white hover:opacity-90 transition-opacity flex items-center gap-2"
                  >
                    {{ item.isFree ? 'Descargar' : 'Comprar' }}
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                  </Link>
                </div>
              </div>
            </article>
          </template>
          <template v-else>
            <div class="col-span-full py-24 flex flex-col items-center justify-center text-center bg-[#F4F4EF] rounded border border-[#C9C7B8]/30">
              <svg class="w-16 h-16 text-[#C9C7B8] mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              <h3 class="text-2xl font-serif font-bold text-[#1A1C19] mb-3">No hay coincidencias</h3>
              <p class="text-[#5F5E5E] max-w-md font-sans mb-8">No hemos encontrado libros que coincidan con tu búsqueda actual.</p>
              <button @click="() => { selectedCategory='Todas'; sortBy='Mas reciente'; }" class="text-xs uppercase tracking-[0.05em] font-bold text-[#57572A] border-b border-[#57572A] pb-1 hover:text-[#1A1C19] hover:border-[#1A1C19] transition-colors">Limpiar Filtros</button>
            </div>
          </template>
        </div>
      </section>
    </main>
  </div>
</template>
