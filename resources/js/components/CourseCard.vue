<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Course {
    id: number | string;
    slug: string;
    title: string;
    image?: string;
    category?: { name: string } | string;
    type: 'en vivo' | 'grabado' | 'evento' | 'masterclass';
    price: number;
    sale_price?: number;
    promotion_title?: string;
    start_date?: string;
    start_time?: string;
}

const props = defineProps<{
    course: Course;
    isDashboard?: boolean;
}>();

import { useCart } from '@/composables/useCart';
const { addItem } = useCart();

// Promotion Logic
const hasDiscount = computed(() => {
    return props.course.sale_price && props.course.sale_price > 0 && props.course.sale_price < props.course.price;
});

const savings = computed(() => {
    if (!hasDiscount.value) return 0;
    return props.course.price - (props.course.sale_price as number);
});

// Date formatting logic
const dateStatus = computed(() => {
    if (!props.course.start_date) return { label: 'Inicia:', value: 'Pronto', isPast: false };
    
    try {
        const startDateStr = props.course.start_date.includes(' ') 
            ? props.course.start_date 
            : `${props.course.start_date} ${props.course.start_time || '00:00:00'}`;
            
        const startDate = new Date(startDateStr.replace(/-/g, '/'));
        const now = new Date();
        const isPast = now > startDate;

        const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long' };
        const dayMonth = new Intl.DateTimeFormat('es-ES', options).format(startDate);
        
        let timeStr = props.course.start_time;
        if (!timeStr && props.course.start_date.includes(' ')) {
            timeStr = props.course.start_date.split(' ')[1];
        }

        let timeFormatted = '';
        if (timeStr) {
            const [hours, minutes] = timeStr.split(':');
            let h = parseInt(hours);
            const m = minutes || '00';
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12;
            timeFormatted = ` - ${h}:${m} ${ampm}`;
        }
        
        return {
            label: isPast ? 'Inició:' : 'Inicia:',
            value: `${dayMonth}${timeFormatted}`,
            isPast
        };
    } catch (e) {
        return { label: 'Inicia:', value: props.course.start_date, isPast: false };
    }
});

const categoryName = computed(() => {
    if (typeof props.course.category === 'object' && props.course.category !== null) {
        return (props.course.category as any).name;
    }
    return props.course.category || 'Especialización';
});

const courseRoute = computed(() => {
    return route('cursos.show', { 
        slug: props.course.slug, 
        ...(props.isDashboard ? { dashboard: true } : {}) 
    });
});

const handleAddToCart = (e: Event) => {
    e.preventDefault();
    addItem(props.course);
};
</script>

<template>
    <article 
        class="bg-surface-container rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden group hover:shadow-xl hover:border-primary/20 transition-all duration-500 flex flex-col hover:-translate-y-1.5 h-full relative"
    >
        <!-- Promotion Badge -->
        <div v-if="course.promotion_title" class="absolute top-3 right-3 z-20 pointer-events-none">
            <span class="px-3 py-1.5 rounded-full bg-red-500/90 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-wider shadow-lg">
                {{ course.promotion_title }}
            </span>
        </div>

        <!-- Visual Cover -->
        <Link :href="courseRoute" class="relative h-48 w-full block bg-surface-container overflow-hidden">
            <img 
                v-if="course.image" 
                :src="course.image" 
                :alt="course.title" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" 
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
            
            <!-- Type Tag -->
            <div class="absolute top-3 left-3 z-10">
                <span class="px-3 py-1.5 rounded-full bg-black/20 backdrop-blur-md text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-2 border border-white/10">
                    <span class="w-1.5 h-1.5 rounded-full" :class="course.type === 'en vivo' ? 'bg-red-400' : (course.type === 'evento' ? 'bg-purple-400' : 'bg-emerald-400')"></span>
                    {{ course.type === 'en vivo' ? 'EN VIVO' : (course.type === 'evento' ? 'EVENTO' : 'GRABADO') }}
                </span>
            </div>
        </Link>

        <!-- Content -->
        <div class="p-5 flex flex-col flex-1">
            <span class="text-[10px] font-bold text-primary uppercase tracking-[0.2em] mb-2 block">{{ categoryName }}</span>
            
            <Link :href="courseRoute">
                <h3 class="font-serif font-bold text-base text-on-surface leading-snug group-hover:text-primary transition-colors mb-3 line-clamp-2" :title="course.title">
                    {{ course.title }}
                </h3>
            </Link>

            <!-- Date / Access -->
            <div class="flex items-center gap-2 text-xs mb-auto" :class="dateStatus.isPast ? 'text-red-500' : 'text-on-surface-variant/60'">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ course.type === 'grabado' ? 'Acceso 24/7 de por vida' : `${dateStatus.label} ${dateStatus.value}` }}
            </div>

            <!-- Pricing + Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-outline-variant/10 mt-4">
                <div>
                    <div v-if="hasDiscount" class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs text-on-surface-variant/40 line-through">S/ {{ course.price }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-red-500/10 text-[9px] text-red-500 font-bold uppercase">-S/ {{ savings }}</span>
                    </div>
                    <span class="text-3xl font-serif font-bold text-on-surface tracking-tight">
                        <span class="text-base text-on-surface-variant/50 mr-0.5">S/</span>{{ hasDiscount ? course.sale_price : course.price }}
                    </span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button 
                        @click="handleAddToCart"
                        class="w-10 h-10 rounded-xl bg-surface border border-outline-variant/15 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all"
                        title="Agregar al carrito"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </button>
                    <Link 
                        :href="courseRoute" 
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-primary text-on-primary text-xs font-bold hover:shadow-md hover:-translate-y-0.5 transition-all"
                    >
                        Ver curso
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </Link>
                </div>
            </div>
        </div>
    </article>
</template>




