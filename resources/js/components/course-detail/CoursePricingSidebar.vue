<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Course } from '@/types/course';
import CoursePaymentModal from './CoursePaymentModal.vue';

const props = defineProps<{
    course: Course;
    isEnrolled?: boolean;
    isDashboard?: boolean;
    hasPendingPayment?: boolean;
}>();

const emit = defineEmits<{
    (e: 'addToCart', event: Event): void;
}>();

function formatPrice(price: number) {
    return Number(price).toFixed(2);
}

const mainPrice = props.course.sale_price && props.course.sale_price > 0 ? props.course.sale_price : props.course.price;
const originalPrice = props.course.sale_price && props.course.sale_price > 0 ? props.course.price : null;

const showPaymentModal = ref(false);
</script>

<template>
    <div class="bg-white rounded p-8 shadow-[0_20px_40px_rgba(26,28,25,0.06)] backdrop-blur-3xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-surface-container-highest rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <h3 class="text-[11px] font-bold text-on-surface-variant tracking-[0.1em] uppercase mb-4">Inscripción Oficial</h3>
        <div class="mb-8 flex flex-col items-start gap-1">
            <span v-if="originalPrice && mainPrice > 0" class="text-[13px] text-outline-variant line-through font-bold tracking-widest">PEN {{ formatPrice(originalPrice) }}</span>
            <div v-if="mainPrice > 0" class="flex items-end gap-2 text-primary">
                <span class="text-xl font-bold leading-none mb-1">S/</span>
                <span class="text-5xl font-serif font-bold leading-none tracking-tight">{{ formatPrice(mainPrice) }}</span>
            </div>
            <div v-else class="flex items-end gap-2 text-primary">
                <span class="text-5xl font-serif font-bold leading-none tracking-tight uppercase">GRATIS</span>
            </div>
        </div>

        <div v-if="isEnrolled">
            <Link :href="route('student.classroom', { course: course.slug })" class="w-full bg-primary text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all mb-4 flex items-center justify-center gap-3 hover:-translate-y-0.5">
                IR AL AULA VIRTUAL
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </Link>
        </div>
        <a v-else-if="course.type === 'evento'" :href="course.whatsapp_link || '#'" target="_blank" class="w-full bg-gradient-to-br from-[#25D366] to-[#128C7E] text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all mb-4 flex items-center justify-center gap-3 hover:-translate-y-0.5">
            UNIRSE POR WHATSAPP
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.225 5.225 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.015-1.04 2.476 0 1.46 1.065 2.871 1.213 3.07.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        <div v-else class="space-y-2">
            <template v-if="$page.props.auth.user && mainPrice > 0">
                <Link v-if="hasPendingPayment" :href="route('student.payments.index')" class="w-full bg-amber-500 text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-3">
                    Tienes un pago pendiente
                </Link>
                <button v-else @click="showPaymentModal = true" class="w-full bg-primary text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-3 hover:-translate-y-0.5">
                    Registrar mi pago
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </template>
            <template v-else-if="$page.props.auth.user && mainPrice <= 0">
                <Link as="button" method="post" :href="route('student.courses.enroll-free', course.slug)" class="w-full bg-primary text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all mb-4 flex items-center justify-center gap-3 hover:-translate-y-0.5">
                    ACCEDER GRATIS
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </Link>
            </template>
            <template v-else-if="!$page.props.auth.user">
                <Link :href="route('login')" class="w-full bg-primary text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-3 hover:-translate-y-0.5">
                    Inicia sesión para acceder
                </Link>
            </template>
            <button v-if="mainPrice > 0" @click="emit('addToCart', $event)" class="w-full bg-surface-container text-on-surface-variant font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded hover:bg-surface-container-high transition-all flex items-center justify-center gap-3 border border-outline-variant/20">
                Añadir al Carrito
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            </button>
            <Link v-if="mainPrice > 0" :href="route('student.payments.index')" class="w-full text-center text-[11px] font-bold text-primary hover:underline block py-1 mt-2">¿Ya pagaste? Subir comprobante</Link>
        </div>

        <p v-if="course.type === 'evento'" class="text-[11px] text-on-surface-variant mb-10 font-medium text-center tracking-wide mt-6">Al unirte al grupo tendrás todos los detalles e información para conectarte.</p>
        <p v-else class="text-[11px] text-on-surface-variant mb-10 font-medium text-center italic tracking-wide mt-6">Tras validar tu pago, activaremos tu acceso al campus institucional.</p>

        <div class="space-y-4 pt-6 border-t border-surface-container-highest">
            <h4 class="font-bold text-[11px] uppercase tracking-[0.1em] text-on-background mb-4">Garantía del Instituto</h4>
            <ul class="text-[13px] text-on-surface-variant space-y-3 font-sans leading-relaxed">
                <li class="flex items-start gap-3"><div class="min-w-1.5 h-1.5 rounded-full bg-primary mt-1.5"></div>Desarrollo curricular respaldado por casos aplicados.</li>
                <li class="flex items-start gap-3"><div class="min-w-1.5 h-1.5 rounded-full bg-primary mt-1.5"></div>Reconocimiento académico de trayectoria.</li>
            </ul>
        </div>
        
        <CoursePaymentModal
            v-if="showPaymentModal"
            :show="showPaymentModal"
            :course="course"
            :amount="mainPrice"
            @close="showPaymentModal = false"
        />
    </div>
</template>
