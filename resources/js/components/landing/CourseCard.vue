<script setup lang="ts">
import { ref } from 'vue';

interface Props {
    title: string;
    description?: string;
    category: string;
    instructor?: string;
    students?: number;
    duration?: string;
    level?: 'Principiante' | 'Intermedio' | 'Avanzado';
    rating?: number;
    reviews?: number;
    type: 'live' | 'recorded' | 'hybrid';
    price: number;
    categoryBgColor?: string;
    image?: string;
}

const {
    title,
    description,
    category,
    instructor,
    students,
    duration,
    level,
    rating,
    type,
    price,
    categoryBgColor,
    image,
} = withDefaults(defineProps<Props>(), {
    categoryBgColor: 'bg-secondary-container',
    students: 0,
    duration: '12 semanas',
    level: 'Intermedio',
    rating: 4.8,
    description: 'Programa especializado diseñado para profesionales que buscan avanzar en sus carreras.',
    instructor: 'Docente IEE',
});

const isHovered = ref(false);

const typeIcon = {
    live: 'videocam', // Material icon name
    recorded: 'play_circle',
    hybrid: 'groups',
};

const typeLabel = {
    live: 'Clases en Vivo',
    recorded: 'Grabado / On-Demand',
    hybrid: 'Presencial / Híbrido',
};

const levelColor = {
    'Principiante': 'text-green-600 bg-green-50 border-green-200',
    'Intermedio': 'text-amber-600 bg-amber-50 border-amber-200',
    'Avanzado': 'text-red-600 bg-red-50 border-red-200',
};
</script>

<template>
    <div 
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        :class="[
            'relative bg-surface-container-lowest rounded-2xl overflow-hidden',
            'border border-outline-variant/10 transition-all duration-500 transform',
            'hover:shadow-2xl hover:-translate-y-2 hover:border-primary/20',
            'flex flex-col h-full'
        ]"
    >
        <!-- Image Section -->
        <div class="relative overflow-hidden h-48 bg-gradient-to-br from-primary/10 to-tertiary-container/10">
            <img
                v-if="image"
                :src="image"
                :alt="title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-primary/20 via-primary-container/10 to-transparent flex items-center justify-center">
                <svg class="w-12 h-12 opacity-20 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 6V4a2 2 0 00-2-2H9a2 2 0 00-2 2v2M5 6h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z" />
                </svg>
            </div>

            <!-- Overlay Badge -->
            <div class="absolute top-4 left-4 flex items-center gap-2">
                <span :class="[
                    categoryBgColor,
                    'px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-widest shadow-lg'
                ]">
                    {{ category }}
                </span>
            </div>

            <!-- Rating Badge -->
            <div v-if="rating" class="absolute top-4 right-4 flex items-center gap-1.5 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-lg">
                <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-xs font-bold text-on-surface">{{ rating }}</span>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6 flex-1 flex flex-col">
            <!-- Meta Info -->
            <div class="flex items-center justify-between mb-3 text-xs font-medium text-on-surface-variant">
                <span class="flex items-center gap-1">
                    <svg v-if="typeIcon[type] === 'videocam'" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 7h8a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />
                    </svg>
                    <svg v-else-if="typeIcon[type] === 'play_circle'" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                        <polygon points="10,8 16,12 10,16" fill="currentColor"/>
                    </svg>
                    <svg v-else-if="typeIcon[type] === 'groups'" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ typeLabel[type] }}
                </span>
                <span v-if="level" :class="['px-2 py-1 rounded border', levelColor[level]]">
                    {{ level }}
                </span>
            </div>

            <!-- Title -->
            <h3 class="font-serif text-xl font-bold text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                {{ title }}
            </h3>

            <!-- Description -->
            <p class="text-on-surface-variant text-sm mb-4 line-clamp-2 flex-shrink-0">
                {{ description }}
            </p>

            <!-- Additional Info -->
            <div class="grid grid-cols-2 gap-3 mb-4 py-4 border-y border-outline-variant/10 text-xs">
                <div>
                    <p class="text-on-surface-variant font-medium mb-1">Duración</p>
                    <p class="text-on-surface font-bold">{{ duration }}</p>
                </div>
                <div>
                    <p class="text-on-surface-variant font-medium mb-1">Estudiantes</p>
                    <p class="text-on-surface font-bold">{{ students }}+</p>
                </div>
            </div>

            <!-- Instructor -->
            <div class="flex items-center gap-2 mb-6 text-xs">
                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
                <span class="text-on-surface-variant">{{ instructor }}</span>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-outline-variant/10 mt-auto">
                <div>
                    <p class="text-xs text-on-surface-variant font-medium mb-1">Inversión</p>
                    <p class="text-2xl font-bold text-primary">S/ {{ price }}</p>
                </div>
                <button :class="[
                    'w-14 h-14 rounded-full flex items-center justify-center',
                    'border-2 border-primary transition-all duration-300',
                    'hover:bg-primary hover:text-on-primary hover:scale-110',
                    'active:scale-95'
                ]">
                    <svg class="w-6 h-6 text-primary group-hover:text-on-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Hover Action -->
            <Transition enter-active-class="transition duration-300" enter-from-class="-translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100">
                <button v-if="isHovered" class="absolute inset-0 bg-gradient-to-t from-primary via-primary/80 to-transparent flex items-end justify-center pb-6 rounded-2xl">
                    <span class="text-on-primary font-bold text-lg">Inscribirse Ahora</span>
                </button>
            </Transition>
        </div>
    </div>
</template>
