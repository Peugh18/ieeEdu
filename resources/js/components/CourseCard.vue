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

const emit = defineEmits(['add-to-cart']);

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

const handleAddToCart = () => {
    emit('add-to-cart', props.course);
};
</script>

<template>
    <article 
        class="bg-white rounded-[2.5rem] border border-outline-variant/20 shadow-sm overflow-hidden group hover:shadow-[0_20px_50px_rgba(87,87,42,0.15)] transition-all duration-700 flex flex-col hover:-translate-y-3 border-opacity-30 h-full relative"
    >
        <!-- SPECTACULAR Promotion Badge -->
        <div v-if="course.promotion_title" class="absolute top-4 right-4 z-20 pointer-events-none">
            <div class="relative">
                <!-- Glossy Background with Shimmer -->
                <div class="absolute inset-0 bg-gradient-to-r from-orange-600 via-red-500 to-orange-600 rounded-full blur-sm opacity-50"></div>
                <div class="relative px-5 py-2 rounded-full bg-gradient-to-r from-orange-600 via-red-500 to-orange-600 shadow-xl overflow-hidden flex items-center gap-2 border border-white/30 backdrop-blur-md">
                    <!-- Shimmering Light Effect -->
                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/40 to-transparent -translate-x-full animate-shimmer"></div>
                    
                    <span class="text-white text-[10px] font-black uppercase tracking-[0.15em] flex items-center gap-1.5 drop-shadow-sm">
                        <svg class="w-3.5 h-3.5 fill-white animate-bounce-slow" viewBox="0 0 24 24">
                            <path d="M17.5 19c-.5 0-.9-.2-1.2-.5-.3-.3-.4-.7-.4-1.2 0-.5.2-.9.5-1.2.3-.3.7-.4 1.2-.4.5 0 .9.2 1.2.5.3.3.4.7.4 1.2 0 .5-.2.9-.5 1.2-.4.3-.8.4-1.2.4zM5 19l4.5-9 4.5 9H5zm1.5-1.5h6l-3-6-3 6zM11 6c-2.8 0-5 2.2-5 5h2c0-1.7 1.3-3 3-3s3 1.3 3 3c0 .3 0 .7-.1 1l1.9.4c.1-.4.2-.9.2-1.4 0-2.8-2.2-5-5-5z"/>
                        </svg>
                        {{ course.promotion_title }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Visual Cover -->
        <Link :href="courseRoute" class="relative h-[230px] w-full block bg-[#F4F4EF] overflow-hidden">
            <img 
                v-if="course.image" 
                :src="course.image" 
                :alt="course.title" 
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-in-out" 
            />
            <div class="absolute inset-0 bg-gradient-to-t from-[#1A1C19]/40 via-transparent to-transparent"></div>
            
            <!-- Type Tag -->
            <div class="absolute top-4 left-4 z-10">
                <span class="px-4 py-2 rounded-full bg-white/10 backdrop-blur-xl text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-2 border border-white/20">
                    <span class="w-2 h-2 rounded-full shadow-[0_0_8px_rgba(255,255,255,0.8)]" :class="course.type === 'en vivo' ? 'bg-[#FF4D4D]' : (course.type === 'evento' ? 'bg-[#9C27B0]' : 'bg-[#4CAF50]')"></span>
                    {{ course.type === 'en vivo' ? 'EN VIVO' : (course.type === 'evento' ? 'EVENTO' : 'GRABADO') }}
                </span>
            </div>
        </Link>

        <!-- Academic Content -->
        <div class="p-8 flex flex-col flex-1">
            <span class="text-[10px] font-bold text-[#57572A]/80 uppercase tracking-[0.2em] mb-3 block">{{ categoryName }}</span>
            
            <Link :href="courseRoute">
                <h3 class="font-serif font-bold text-2xl text-[#1A1C19] leading-tight group-hover:text-[#57572A] transition-colors italic mb-4 line-clamp-2" :title="course.title">
                    {{ course.title }}
                </h3>
            </Link>

            <!-- Features -->
            <div class="space-y-2 flex-1">
                <div class="flex items-center gap-2.5 text-[13px] font-medium italic" :class="dateStatus.isPast ? 'text-red-600' : 'text-[#5F5E5E]'">
                    <span class="material-symbols-outlined text-[18px] opacity-60">calendar_today</span>
                    {{ course.type === 'grabado' ? 'Acceso 24/7 de por vida' : `${dateStatus.label} ${dateStatus.value}` }}
                </div>
            </div>

            <!-- SPECTACULAR Pricing Section -->
            <div class="flex flex-col pt-8 border-t border-[#F0F0E8] gap-8 mt-4">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col gap-1">
                        <div v-if="hasDiscount" class="flex items-center gap-3">
                            <span class="text-sm text-[#5F5E5E]/40 line-through font-serif italic">S/ {{ course.price }}</span>
                            <span class="px-2 py-0.5 rounded bg-orange-100 text-[10px] text-orange-700 font-bold uppercase tracking-wider">Ahorra S/ {{ savings }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-4xl font-serif font-bold text-[#1A1C19] italic tracking-tight">
                                <span class="text-xl align-top mt-1 inline-block mr-1 opacity-50 font-sans tracking-normal">S/</span>
                                {{ hasDiscount ? course.sale_price : course.price }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <!-- Square Cart Button -->
                    <button 
                        @click="handleAddToCart"
                        class="group/cart relative flex items-center justify-center w-14 h-14 rounded-2xl bg-[#F4F4EF] hover:bg-[#D4AF37] transition-all duration-500 shadow-sm hover:shadow-[0_10px_20px_rgba(212,175,55,0.3)] hover:-translate-y-1"
                        title="Agregar al carrito"
                    >
                        <span class="material-symbols-outlined text-[#57572A] group-hover/cart:text-white transition-colors duration-300">shopping_cart</span>
                        
                        <!-- Mini Tooltip on Hover -->
                        <span class="absolute -top-12 left-1/2 -translate-x-1/2 px-3 py-1 bg-[#1A1C19] text-white text-[10px] font-bold rounded-lg opacity-0 group-hover/cart:opacity-100 transition-opacity pointer-events-none whitespace-nowrap after:content-[''] after:absolute after:top-full after:left-1/2 after:-translate-x-1/2 after:border-8 after:border-transparent after:border-t-[#1A1C19]">
                            Añadir al carrito
                        </span>
                    </button>

                    <!-- Wide Details Button -->
                    <Link 
                        :href="courseRoute" 
                        class="group/details relative flex-1 h-14 flex items-center justify-center gap-3 rounded-2xl bg-[#57572A] text-white text-[11px] font-black uppercase tracking-[0.15em] shadow-lg hover:shadow-[0_15px_30px_rgba(87,87,42,0.3)] transition-all duration-500 hover:-translate-y-1 overflow-hidden"
                    >
                        <!-- Button Shimmer / Glow Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/details:translate-x-full transition-transform duration-1000"></div>
                        
                        <span>Ver detalles</span>
                        <span class="material-symbols-outlined text-[18px] group-hover/details:translate-x-1 transition-transform">arrow_forward</span>
                    </Link>
                </div>
            </div>
        </div>
    </article>
</template>

<style scoped>
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.animate-shimmer {
    animation: shimmer 2s infinite linear;
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-2px) scale(1.1); }
}

.animate-bounce-slow {
    animation: bounce-slow 2s infinite ease-in-out;
}
</style>



