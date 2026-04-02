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
    type: 'en vivo' | 'grabado' | 'hibrido' | 'masterclass' | 'evento';
    price: number;
    sale_price?: number;
    categoryBgColor?: string;
    image?: string;
    instructor_image?: string;
    start_date?: string;
    start_time?: string;
    class_hours?: number;
    whatsapp_link?: string;
}

const props = withDefaults(defineProps<Props>(), {
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
    'en vivo': 'videocam',
    'grabado': 'play_circle',
    'hibrido': 'groups',
    'masterclass': 'record_voice_over',
    'evento': 'event',
};

const typeLabel = {
    'en vivo': 'Clases en Vivo',
    'grabado': 'Grabado / On-Demand',
    'hibrido': 'Presencial / Híbrido',
    'masterclass': 'Masterclass',
    'evento': 'Evento Especial',
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
                v-if="props.image"
                :src="props.image"
                :alt="props.title"
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
                    props.categoryBgColor,
                    'px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-widest shadow-lg'
                ]">
                    {{ props.category }}
                </span>
            </div>

            <!-- Rating Badge -->
            <div v-if="props.rating" class="absolute top-4 right-4 flex items-center gap-1.5 bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-lg">
                <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-xs font-bold text-on-surface">{{ props.rating }}</span>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-6 flex-1 flex flex-col">
            <!-- Meta Info -->
            <div class="flex items-center justify-between mb-3 text-xs font-medium text-on-surface-variant">
                <span class="flex items-center gap-1">
                    <svg v-if="typeIcon[props.type] === 'videocam'" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 7h8a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />
                    </svg>
                    <svg v-else-if="typeIcon[props.type] === 'play_circle'" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                        <polygon points="10,8 16,12 10,16" fill="currentColor"/>
                    </svg>
                    <svg v-else-if="typeIcon[props.type] === 'groups'" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ typeLabel[props.type] }}
                </span>
                <span v-if="props.level" :class="['px-2 py-1 rounded border', levelColor[props.level]]">
                    {{ props.level }}
                </span>
            </div>

            <!-- Title -->
            <h3 class="font-serif text-xl font-bold text-on-surface mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                {{ props.title }}
            </h3>

            <!-- Description -->
            <p class="text-on-surface-variant text-sm mb-4 line-clamp-2 flex-shrink-0">
                {{ props.description }}
            </p>

            <!-- Additional Info based on Type -->
            <div class="grid grid-cols-2 gap-3 mb-4 py-4 border-y border-outline-variant/10 text-xs">
                <!-- En Vivo / Masterclass -->
                <template v-if="props.type === 'en vivo' || props.type === 'masterclass' || props.type === 'evento'">
                    <div>
                        <p class="text-on-surface-variant font-medium mb-1">Inicio</p>
                        <p class="text-on-surface font-bold text-[#4B5320] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                            {{ props.start_date ? new Date(props.start_date).toLocaleDateString() : 'Por definir' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-on-surface-variant font-medium mb-1">Hora</p>
                        <p class="text-on-surface font-bold text-[#4B5320] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">schedule</span>
                            {{ props.start_time || 'Por definir' }}
                        </p>
                    </div>
                </template>

                <!-- Grabado -->
                <template v-else-if="props.type === 'grabado'">
                    <div class="col-span-2">
                        <p class="text-on-surface-variant font-medium mb-1">Modalidad</p>
                        <p class="text-on-surface font-bold text-[#4B5320] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">update</span>
                            A tu propio ritmo
                        </p>
                    </div>
                </template>
            </div>

            <!-- Instructor removed per request -->

            <!-- Footer -->
            <div v-if="props.type !== 'masterclass' && props.type !== 'evento'" class="flex items-center justify-between pt-4 border-t border-outline-variant/10 mt-auto">
                <div class="flex flex-col">
                    <p class="text-xs text-on-surface-variant font-medium mb-1">Inversión</p>
                    <div class="flex items-baseline gap-2">
                        <template v-if="props.sale_price && props.sale_price > 0 && props.sale_price < props.price">
                            <p class="text-2xl font-bold text-[#4B5320]">S/ {{ props.sale_price }}</p>
                            <p class="text-sm font-medium text-on-surface-variant line-through">S/ {{ props.price }}</p>
                        </template>
                        <template v-else-if="props.price > 0">
                            <p class="text-2xl font-bold text-[#4B5320]">S/ {{ props.price }}</p>
                        </template>
                        <p class="text-2xl font-bold text-[#4B5320] uppercase" v-else>Gratis</p>
                    </div>
                </div>
            </div>

            <!-- Hover Action -->
            <Transition enter-active-class="transition duration-300" enter-from-class="-translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100">
                <div v-if="isHovered" class="absolute inset-0 bg-gradient-to-t from-[#4B5320]/95 via-[#4B5320]/80 to-transparent flex items-end justify-center pb-6 rounded-2xl px-4">
                    <div v-if="props.type === 'masterclass' || props.type === 'evento'" class="w-full h-full flex flex-col pt-32 pb-4 justify-end gap-3 relative z-10 px-4">
                        <a :href="props.whatsapp_link || '#'" target="_blank" class="w-full flex items-center justify-center gap-2 bg-[#25D366] text-white py-3 rounded-xl text-[13px] font-bold uppercase tracking-widest hover:bg-[#20BE5A] transition shadow-lg">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.115.55 4.183 1.597 6l-1.6 5.8 5.922-1.556c1.782.973 3.791 1.488 5.85 1.488 6.645 0 12.03-5.385 12.03-12.03S18.676 0 12.031 0zm0 21.905c-1.801 0-3.56-.484-5.111-1.405l-.367-.217-3.793.996 1.002-3.693-.238-.38c-.997-1.59-1.523-3.425-1.523-5.32 0-5.632 4.58-10.213 10.212-10.213 5.631 0 10.212 4.581 10.212 10.213 0 5.632-4.58 10.213-10.212 10.213zm5.602-7.662c-.307-.154-1.815-.898-2.097-1.002-.282-.104-.488-.154-.693.154-.206.308-.792 1.002-.971 1.206-.18.205-.36.23-.667.077-.308-.154-1.295-.478-2.467-1.525-.912-.815-1.528-1.821-1.708-2.129-.18-.308-.02-.475.134-.627.139-.138.307-.358.461-.537.154-.18.205-.308.308-.513.102-.205.051-.385-.026-.539-.077-.154-.693-1.673-.95-2.289-.25-.6-.505-.518-.694-.527-.18-.009-.385-.011-.59-.011-.205 0-.539.077-.821.385-.282.308-1.077 1.053-1.077 2.568 0 1.515 1.103 2.978 1.257 3.183.154.205 2.174 3.321 5.265 4.659.735.318 1.308.508 1.758.65.736.234 1.405.2 1.933.121.59-.089 1.815-.742 2.072-1.46.257-.719.257-1.335.18-1.461-.077-.126-.282-.203-.59-.357z"/></svg>
                            Unirse al grupo
                        </a>
                        <!-- Botón pequeño, redondo: Ver Detalles. Redondo => rounded-full -->
                        <button class="w-full flex items-center justify-center bg-white/10 text-white backdrop-blur-sm py-2 rounded-full text-[10px] uppercase font-bold tracking-widest hover:bg-white/20 transition">
                            <span class="material-symbols-outlined text-[16px] mr-1">info</span>
                            Ver detalles
                        </button>
                    </div>
                    <div v-else class="w-full flex flex-col gap-2 relative z-10">
                        <button class="w-full flex items-center justify-center gap-2 bg-[#D4AF37] text-white py-3 rounded-xl font-bold hover:bg-[#C5A017] transition shadow-lg">
                            <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                            Agregar al carrito
                        </button>
                        <button class="w-full flex items-center justify-center bg-white/10 text-white backdrop-blur-sm py-2 rounded-xl text-sm font-semibold hover:bg-white/20 transition">Ver detalles</button>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>
