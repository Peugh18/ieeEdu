<script setup lang="ts">
import { useCart } from '@/composables/useCart';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const { addItem } = useCart();

const patternId = computed(() => `card-pattern-${props.course.id}`);

// Promotion Logic
const hasDiscount = computed(() => {
    return props.course.sale_price && props.course.sale_price > 0 && props.course.sale_price < props.course.price;
});

const savings = computed(() => {
    if (!hasDiscount.value) return 0;
    const diff = props.course.price - (props.course.sale_price as number);
    return Math.round(diff * 100) / 100;
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
            isPast,
        };
    } catch {
        return { label: 'Inicia:', value: props.course.start_date, isPast: false };
    }
});

const categoryName = computed(() => {
    if (typeof props.course.category === 'object' && props.course.category !== null) {
        return (props.course.category as { name: string }).name;
    }
    return props.course.category || 'Especialización';
});

const courseRoute = computed(() => {
    return route('cursos.show', {
        slug: props.course.slug,
        ...(props.isDashboard ? { dashboard: true } : {}),
    });
});

const handleAddToCart = (e: Event) => {
    e.preventDefault();
    addItem(props.course);
};
</script>

<template>
    <article
        class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-outline-variant/15 bg-surface-container shadow-sm transition-all duration-500 hover:-translate-y-1.5 hover:border-primary/20 hover:shadow-xl"
    >
        <!-- Promotion Badge -->
        <div v-if="course.promotion_title" class="pointer-events-none absolute right-3 top-3 z-20">
            <span class="rounded-full bg-red-500/90 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-lg backdrop-blur-sm">
                {{ course.promotion_title }}
            </span>
        </div>

        <!-- Visual Cover -->
        <Link :href="courseRoute" class="relative block h-48 w-full overflow-hidden border-b border-outline-variant/10 bg-surface-container-high">
            <img
                v-if="course.image"
                :src="course.image"
                :alt="course.title"
                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
            <!-- Premium Placeholder Fallback when no image is provided -->
            <div
                v-else
                class="relative flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 via-primary/5 to-surface-container-highest transition-transform duration-700 group-hover:scale-105"
            >
                <!-- Decorative subtle geometric background grid -->
                <svg class="absolute inset-0 size-full stroke-primary/[0.04]" fill="none">
                    <defs>
                        <pattern :id="patternId" x="0" y="0" width="12" height="12" patternUnits="userSpaceOnUse">
                            <path d="M-1 5L5 -1M3 9L8.5 3.5" stroke-width="0.5"></path>
                        </pattern>
                    </defs>
                    <rect stroke="none" :fill="`url(#${patternId})`" width="100%" height="100%"></rect>
                </svg>
                <!-- Central Icon -->
                <div
                    class="relative flex h-12 w-12 items-center justify-center rounded-2xl border border-primary/20 bg-primary/10 text-primary/60 shadow-inner"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                </div>
            </div>

            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/5 to-transparent"></div>

            <!-- Type Tag -->
            <div class="absolute left-3 top-3 z-10">
                <span
                    class="flex items-center gap-2 rounded-full border border-white/10 bg-black/30 px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white shadow-sm backdrop-blur-md"
                >
                    <span
                        class="h-1.5 w-1.5 rounded-full"
                        :class="course.type === 'en vivo' ? 'bg-red-400' : course.type === 'evento' ? 'bg-purple-400' : 'bg-emerald-400'"
                    ></span>
                    {{ course.type === 'en vivo' ? 'EN VIVO' : course.type === 'evento' ? 'EVENTO' : 'GRABADO' }}
                </span>
            </div>
        </Link>

        <!-- Content -->
        <div class="flex flex-1 flex-col p-5">
            <span class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-primary">{{ categoryName }}</span>

            <Link :href="courseRoute">
                <h3
                    class="mb-3 line-clamp-2 flex min-h-[2.75rem] items-start font-serif text-base font-bold leading-snug text-on-surface transition-colors group-hover:text-primary"
                    :title="course.title"
                >
                    {{ course.title }}
                </h3>
            </Link>

            <!-- Date / Access -->
            <div class="mb-auto flex items-center gap-2 text-xs" :class="dateStatus.isPast ? 'text-red-500' : 'text-on-surface-variant/60'">
                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>
                {{ course.type === 'grabado' ? 'Acceso 24/7 de por vida' : `${dateStatus.label} ${dateStatus.value}` }}
            </div>

            <!-- Pricing + Actions -->
            <div class="mt-4 space-y-4 border-t border-outline-variant/10 pt-4">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <span class="font-serif text-3xl font-bold tracking-tight text-on-surface">
                        <span class="mr-0.5 text-base text-on-surface-variant/50">S/</span>{{ hasDiscount ? course.sale_price : course.price }}
                    </span>
                    <div v-if="hasDiscount" class="flex items-center gap-2">
                        <span class="text-xs text-on-surface-variant/40 line-through">S/ {{ course.price }}</span>
                        <span class="rounded bg-red-500/10 px-1.5 py-0.5 text-[9px] font-bold uppercase text-red-500">-S/ {{ savings }}</span>
                    </div>
                </div>

                <div class="flex w-full items-center gap-2">
                    <button
                        @click="handleAddToCart"
                        class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl border border-outline-variant/15 bg-surface text-on-surface-variant transition-all hover:border-primary/30 hover:bg-primary/5 hover:text-primary"
                        title="Agregar al carrito"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"
                            />
                        </svg>
                    </button>
                    <Link
                        :href="courseRoute"
                        class="inline-flex h-11 flex-1 items-center justify-center gap-1.5 rounded-xl bg-primary px-5 py-2.5 text-center text-xs font-bold text-on-primary transition-all hover:-translate-y-0.5 hover:shadow-md"
                    >
                        Ver curso
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </article>
</template>
