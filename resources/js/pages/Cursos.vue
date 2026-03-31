<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

interface Curso {
    id: number;
    title: string;
    description: string;
    category: string;
    instructor: string;
    students: number;
    duration: string;
    level: string;
    rating: number;
    reviews: number;
    type: 'live' | 'recorded' | 'hybrid';
    price: number;
    image: string;
}

const cursos = ref<Curso[]>([
    {
        id: 1,
        title: 'Diplomado en Gestión Estratégica del Estado',
        description: 'Programa intensivo para líderes del sector público con enfoque en políticas públicas.',
        category: 'Gestión Pública',
        instructor: 'Mg. Roberto Flores',
        students: 320,
        duration: '16 semanas',
        level: 'Avanzado',
        rating: 4.9,
        reviews: 156,
        type: 'hybrid',
        price: 1200,
        image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&h=500&fit=crop',
    },
    {
        id: 2,
        title: 'Análisis de Datos para Decisiones Empresariales',
        description: 'Domina herramientas de análisis y visualización de datos empresariales.',
        category: 'Finanzas',
        instructor: 'Dr. Carlos Mendoza',
        students: 245,
        duration: '12 semanas',
        level: 'Intermedio',
        rating: 4.8,
        reviews: 98,
        type: 'recorded',
        price: 850,
        image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&h=500&fit=crop',
    },
    {
        id: 3,
        title: 'Programa de Alta Gerencia y Comunicación',
        description: 'Desarrolla habilidades de liderazgo ejecutivo y comunicación corporativa.',
        category: 'Liderazgo',
        instructor: 'Dra. Patricia Silva',
        students: 187,
        duration: '20 semanas',
        level: 'Avanzado',
        rating: 4.95,
        reviews: 234,
        type: 'live',
        price: 2400,
        image: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&h=500&fit=crop',
    },
]);

const filters = ref({
    category: 'Todas',
    type: 'Todas',
    level: 'Todos',
});

const orderBy = ref('Más reciente');

const categories = ['Todas', 'Gestión Pública', 'Finanzas', 'Liderazgo'];
const types = ['Todas', 'Grabado', 'En vivo', 'Híbrido'];
const levels = ['Todos', 'Principiante', 'Intermedio', 'Avanzado'];

const filteredCursos = computed(() => {
    let list = cursos.value;

    if (filters.value.category !== 'Todas') {
        list = list.filter((curso) => curso.category === filters.value.category);
    }

    if (filters.value.type !== 'Todas') {
        const mapType = { 'Grabado': 'recorded', 'En vivo': 'live', 'Híbrido': 'hybrid' };
        list = list.filter((curso) => curso.type === mapType[filters.value.type]);
    }

    if (filters.value.level !== 'Todos') {
        list = list.filter((curso) => curso.level === filters.value.level);
    }

    if (orderBy.value === 'Menor precio') {
        list = [...list].sort((a, b) => a.price - b.price);
    } else if (orderBy.value === 'Mayor precio') {
        list = [...list].sort((a, b) => b.price - a.price);
    }

    return list;
});
</script>

<template>
    <Head title="IEE - Cursos" />

    <div class="min-h-screen bg-surface text-on-surface">
        <section class="bg-surface-container-low py-16">
            <div class="max-w-7xl mx-auto px-6 md:px-8">
                <div class="text-center mb-10">
                    <p class="text-primary text-xs font-bold uppercase tracking-widest mb-2">Cursos</p>
                    <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-on-surface">
                        Catálogo de <span class="italic text-primary">Cursos</span>
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-on-surface-variant">Explora clases grabadas, en vivo e híbridas, diseñadas para transformar tu trayectoria profesional.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <select v-model="filters.category" class="rounded-lg p-3 border border-outline-variant/40 bg-surface-container-low">
                        <option v-for="category in categories" :key="category">{{ category }}</option>
                    </select>
                    <select v-model="filters.type" class="rounded-lg p-3 border border-outline-variant/40 bg-surface-container-low">
                        <option v-for="type in types" :key="type">{{ type }}</option>
                    </select>
                    <select v-model="filters.level" class="rounded-lg p-3 border border-outline-variant/40 bg-surface-container-low">
                        <option v-for="level in levels" :key="level">{{ level }}</option>
                    </select>
                    <select v-model="orderBy" class="rounded-lg p-3 border border-outline-variant/40 bg-surface-container-low">
                        <option>Más reciente</option>
                        <option>Menor precio</option>
                        <option>Mayor precio</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template v-if="filteredCursos.length > 0">
                        <article
                            v-for="curso in filteredCursos"
                            :key="curso.id"
                            class="bg-white rounded-2xl border border-outline-variant/10 shadow-sm hover:shadow-lg transition-all overflow-hidden"
                        >
                            <img :src="curso.image" :alt="curso.title" class="h-40 w-full object-cover" />
                            <div class="p-5 flex flex-col h-full">
                                <span class="text-xs text-primary font-bold uppercase tracking-widest mb-2">{{ curso.category }}</span>
                                <h2 class="font-serif text-xl text-on-surface font-bold mb-2 line-clamp-2">{{ curso.title }}</h2>
                                <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{{ curso.description }}</p>
                                <ul class="text-xs text-on-surface-variant mb-4 space-y-1">
                                    <li>Instructor: {{ curso.instructor }}</li>
                                    <li>Duración: {{ curso.duration }}</li>
                                    <li>{{ curso.students }} alumnos</li>
                                </ul>
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-2xl font-bold text-primary">S/ {{ curso.price }}</span>
                                    <a href="/cursos/{{ curso.id }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary text-on-primary text-xs font-semibold transition-all hover:opacity-90">
                                        Ver curso <span class="material-symbols-outlined">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </template>
                    <template v-else>
                        <div class="col-span-full p-8 text-center bg-surface-container-high rounded-2xl border border-outline-variant/20">
                            <p class="text-on-surface-variant">No se encontraron cursos con los filtros seleccionados.</p>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    </div>
</template>
