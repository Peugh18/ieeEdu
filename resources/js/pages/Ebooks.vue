<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

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

  <div class="min-h-screen bg-surface text-on-surface">
    <section class="bg-surface-container-low py-16">
      <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="text-center mb-10">
          <p class="text-primary text-xs font-bold uppercase tracking-widest mb-2">Publicaciones</p>
          <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-on-surface">
            Biblioteca de <span class="italic text-primary">Ebooks</span>
          </h1>
          <p class="mt-4 max-w-2xl mx-auto text-on-surface-variant">Descarga contenidos gratuitos o compra libros premium para tu especialización profesional.</p>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
          <div class="flex flex-wrap gap-2">
            <button
              v-for="category in categories"
              :key="category"
              @click="selectedCategory = category"
              :class="['px-4 py-2 rounded-full text-xs font-semibold transition', selectedCategory === category ? 'bg-primary text-on-primary' : 'bg-surface-container-low border border-outline-variant/40 text-on-surface-variant hover:bg-surface']"
            >
              {{ category }}
            </button>
          </div>

          <div class="flex items-center gap-3">
            <span class="text-on-surface-variant text-xs uppercase tracking-widest">Ordenar por:</span>
            <select v-model="sortBy" class="bg-surface-container-low border border-outline-variant/40 rounded-lg px-3 py-2 text-sm">
              <option>Mas reciente</option>
              <option>Menor precio</option>
              <option>Mayor precio</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <template v-if="filteredEbooks.length">
            <article
              v-for="item in filteredEbooks"
              :key="item.id"
              class="bg-white rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all overflow-hidden"
            >
              <div class="h-48 overflow-hidden bg-primary/10">
                <img :src="item.cover" :alt="item.title" class="h-full w-full object-cover" />
              </div>
              <div class="p-5 flex flex-col h-full">
                <span class="text-[11px] font-bold uppercase tracking-wider text-primary mb-2">{{ item.category }}</span>
                <h2 class="font-serif text-lg lg:text-xl font-bold text-on-surface mb-2 line-clamp-2">{{ item.title }}</h2>
                <p class="text-xs text-on-surface-variant mb-4">Autor: {{ item.author }} · {{ item.pages }} páginas · {{ item.size }}</p>

                <div class="mt-auto flex items-center justify-between">
                  <div class="text-2xl font-bold text-primary">
                    {{ item.isFree ? 'Gratis' : `S/ ${item.price}` }}
                  </div>
                  <a
                    :href="item.isFree ? item.href : item.href"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary text-on-primary text-xs font-semibold hover:opacity-90 transition-all"
                  >
                    {{ item.isFree ? 'Descargar' : 'Comprar' }}
                    <span class="material-symbols-outlined text-sm">east</span>
                  </a>
                </div>
              </div>
            </article>
          </template>
          <template v-else>
            <div class="col-span-full p-8 text-center bg-surface-container-high rounded-2xl border border-outline-variant/20">
              <p class="text-on-surface-variant">No hay resultados para la categoría seleccionada.</p>
            </div>
          </template>
        </div>
      </div>
    </section>
  </div>
</template>
