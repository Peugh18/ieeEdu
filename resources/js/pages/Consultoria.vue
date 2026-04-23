<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Navigation from '../components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps<{
    isDashboard?: boolean;
    banner?: {
        heading: string | null;
        subheading: string | null;
        image_path: string | null;
        button_text: string | null;
        button_link: string | null;
        position: string | null;
        whatsapp_number: string | null;
        contact_email: string | null;
        contact_address: string | null;
    } | null;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Consultoría', href: '#' },
];

// Datos del hero con fallback
const heroHeading    = computed(() => props.banner?.heading    || 'Consultoría especializada para el sector empresarial y público');
const heroSubtitle   = computed(() => props.banner?.subheading || 'Soluciones prácticas, técnicas y basadas en evidencia para impulsar la gestión y competitividad de tu organización.');
const heroBg         = computed(() => props.banner?.image_path || '/images/landing/consultoria_hero.jpg');
const heroWhatsapp   = computed(() => props.banner?.whatsapp_number || '51000000000');
const heroEmail      = computed(() => props.banner?.contact_email    || 'info@iee.edu.pe');
const heroAddress    = computed(() => props.banner?.contact_address  || 'Trujillo, La Libertad — Perú');
const whatsappLink   = computed(() => `https://wa.me/${heroWhatsapp.value}?text=Hola%2C%20me%20interesa%20una%20consultor%C3%ADa%20con%20el%20IEE`);

const areas = [
    'Planeamiento empresarial y de negocios',
    'Reorganización empresarial e institucional',
    'Valorización empresarial',
    'Transformación digital',
    'Implementación de sistemas ISO',
    'Oportunidades de inversión y financiamiento',
    'Estructuración de Proyectos (PA, APP, OxI)',
    'Negociación societaria y apalancamiento',
    'Planes territoriales (PDM, DPU, PAT, DET)',
    'Estudios de impacto ambiental y vial',
    'Estudios de impacto socioeconómico y financiero',
    'Informes de dinámica sectorial',
];

const pasos = [
    {
        n: '01',
        titulo: 'Diagnóstico',
        desc: 'Analizamos tu situación actual, identificamos brechas y oportunidades con rigor técnico y metodológico.',
        svg: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>`,
    },
    {
        n: '02',
        titulo: 'Propuesta técnica',
        desc: 'Diseñamos una solución a medida, basada en evidencia y alineada con los objetivos estratégicos de tu organización.',
        svg: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>`,
    },
    {
        n: '03',
        titulo: 'Ejecución y resultados',
        desc: 'Acompañamos la implementación, medimos el impacto real y garantizamos la sostenibilidad de los cambios.',
        svg: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>`,
    },
];

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    area: '',
    message: '',
});

const page = usePage();
const flashSuccess = computed(() => (page.props.flash as any)?.success);

function submitForm() {
    form.post(route('consultoria.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Consultoría Especializada — IEE" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['min-h-screen font-sans', !isDashboard ? 'bg-surface text-on-surface' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="[!isDashboard ? 'pt-20' : 'pt-0']">

                <!-- ═══════════════════════════════════════════
                     HERO MOBILE — compacto, sin imagen, llamativo
                ═══════════════════════════════════════════ -->
                <section class="md:hidden relative overflow-hidden bg-surface-container-low px-6 pt-6 pb-8">
                    <!-- Blob decorativo -->
                    <div class="absolute top-0 right-0 w-48 h-48 bg-primary/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>

                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 border border-primary/20 mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                        <span class="text-[11px] font-bold text-primary uppercase tracking-widest">Consultoría IEE</span>
                    </div>

                    <!-- Título -->
                    <h1 class="font-serif text-[1.75rem] leading-tight font-bold text-on-surface mb-2">
                        ¿Necesitas asesoría<br/>
                        <span class="text-primary italic">especializada?</span>
                    </h1>
                    <p class="text-on-surface-variant text-sm mb-6">Trujillo, Perú · Respondemos en 24h</p>

                    <!-- CTAs de contacto -->
                    <div class="flex flex-col gap-3">
                        <!-- WhatsApp — principal -->
                        <a
                            :href="whatsappLink"
                            target="_blank"
                            rel="noopener"
                            class="flex items-center gap-3 px-4 py-4 rounded-2xl bg-green-500 text-white font-bold shadow-lg shadow-green-500/20"
                        >
                            <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <div class="flex-1">
                                <p class="text-[11px] text-white/80 font-normal">Contáctanos ahora</p>
                                <p class="text-sm font-bold">WhatsApp</p>
                            </div>
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>

                        <!-- Email + Formulario en fila -->
                        <div class="grid grid-cols-2 gap-3">
                            <a
                                :href="`mailto:${heroEmail}`"
                                class="flex items-center gap-2.5 px-4 py-3.5 rounded-2xl bg-surface-container border border-outline-variant/20"
                            >
                                <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <div>
                                    <p class="text-[10px] text-on-surface-variant">Email</p>
                                    <p class="text-xs font-semibold text-on-surface truncate">Escribir</p>
                                </div>
                            </a>
                            <a
                                href="#solicitar"
                                class="flex items-center gap-2.5 px-4 py-3.5 rounded-2xl border border-primary/30 bg-primary/5"
                            >
                                <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <div>
                                    <p class="text-[10px] text-on-surface-variant">Formulario</p>
                                    <p class="text-xs font-semibold text-primary">Solicitar</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- ═══════════════════════════════════════════
                     HERO DESKTOP — foto full-width premium
                ═══════════════════════════════════════════ -->
                <section class="hidden md:block relative overflow-hidden w-full" style="aspect-ratio: 1920 / 600;">
                    <!-- Imagen dinámica desde admin -->
                    <img
                        :src="heroBg"
                        alt="IEE Consultoría"
                        class="absolute inset-0 w-full h-full object-cover object-center scale-105 transition-transform duration-[8s] ease-out"
                        style="animation: heroZoom 8s ease-out forwards;"
                    />
                    <!-- Gradiente multi-capa -->
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.3) 100%);"></div>
                    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 60%);"></div>
                    <!-- Texture overlay sutil -->
                    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%224%22 height=%224%22><rect width=%224%22 height=%224%22 fill=%22white%22/><rect width=%221%22 height=%221%22 fill=%22black%22/></svg>');"></div>

                    <!-- Contenido del hero -->
                    <div class="relative z-10 h-full flex flex-col justify-end max-w-7xl mx-auto px-6 lg:px-8 pb-10 md:pb-14">

                        <!-- Badge institucional -->
                        <div class="flex items-center gap-2.5 mb-5">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-[0.18em] text-white/70 border border-white/15 backdrop-blur-sm" style="background: rgba(255,255,255,0.07);">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse flex-shrink-0"></span>
                                IEE · Desde 2009 · Trujillo, Perú
                            </span>
                        </div>

                        <!-- Título dinámico -->
                        <h1 class="font-serif text-3xl md:text-5xl lg:text-[3.25rem] text-white font-bold leading-[1.12] mb-4 max-w-3xl drop-shadow-2xl">
                            {{ heroHeading }}
                        </h1>

                        <!-- Subtítulo dinámico -->
                        <p class="text-white/65 text-sm md:text-base max-w-xl mb-8 leading-relaxed">
                            {{ heroSubtitle }}
                        </p>

                        <!-- Chips de contacto rápido -->
                        <div class="flex flex-wrap gap-3">
                            <!-- Email dinámico -->
                            <a
                                :href="`mailto:${heroEmail}`"
                                class="group flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all duration-300"
                                style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14); backdrop-filter: blur(8px);"
                                onmouseover="this.style.background='rgba(255,255,255,0.15)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.08)'"
                            >
                                <svg class="w-4 h-4 flex-shrink-0" style="color: var(--md-sys-color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-white text-sm font-medium">{{ heroEmail }}</span>
                            </a>

                            <!-- WhatsApp dinámico -->
                            <a
                                :href="whatsappLink"
                                target="_blank"
                                rel="noopener"
                                class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all duration-300"
                                style="background: rgba(37,211,102,0.15); border: 1px solid rgba(37,211,102,0.25); backdrop-filter: blur(8px);"
                                onmouseover="this.style.background='rgba(37,211,102,0.25)'"
                                onmouseout="this.style.background='rgba(37,211,102,0.15)'"
                            >
                                <svg class="w-4 h-4 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                <span class="text-white text-sm font-medium">WhatsApp</span>
                            </a>

                            <!-- Separador visual -->
                            <div class="w-px bg-white/10 self-stretch mx-1 hidden sm:block"></div>

                            <!-- Ubicación dinámica -->
                            <div class="flex items-center gap-2 px-4 py-2.5 text-white/50 text-sm">
                                <svg class="w-4 h-4 flex-shrink-0 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ heroAddress }}
                            </div>

                            <!-- CTA principal -->
                            <a
                                href="#solicitar"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl ml-auto sm:ml-0"
                                style="background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary);"
                            >
                                Solicitar consultoría
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- ═══════════════════════════════════════════
                     SERVICIOS — lista 2 columnas elegante
                ═══════════════════════════════════════════ -->
                <section class="py-16 md:py-24 bg-surface">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8">
                        <!-- Label -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-primary/40"></div>
                            <span class="text-[11px] font-bold text-primary tracking-[0.22em] uppercase">Portafolio de Servicios</span>
                        </div>

                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
                            <h2 class="font-serif text-3xl md:text-4xl lg:text-5xl text-on-surface leading-tight">
                                ¿En qué podemos <em class="not-italic text-primary">ayudarte?</em>
                            </h2>
                            <a href="#solicitar" class="flex-shrink-0 group inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:gap-3 transition-all underline underline-offset-4 decoration-primary/30 hover:decoration-primary">
                                Solicitar ahora
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-x-16 gap-y-0">
                            <div
                                v-for="(area, i) in areas"
                                :key="area"
                                class="group flex items-center gap-4 py-4 border-b border-outline-variant/15 hover:border-primary/25 transition-all duration-300 cursor-default"
                                :class="{ 'border-t': i === 0 || i === 1 }"
                            >
                                <span class="text-[11px] font-bold text-primary/30 font-mono w-5 flex-shrink-0 group-hover:text-primary transition-colors">{{ String(i + 1).padStart(2, '0') }}</span>
                                <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors leading-snug flex-1">{{ area }}</span>
                                <svg class="w-3.5 h-3.5 text-primary/0 group-hover:text-primary/50 flex-shrink-0 transition-all -translate-x-2 group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ═══════════════════════════════════════════
                     CÓMO TRABAJAMOS — 3 pasos con línea
                ═══════════════════════════════════════════ -->
                <section class="hidden md:block py-16 md:py-20 bg-surface-container-low relative overflow-hidden">
                    <div class="absolute inset-0 pointer-events-none overflow-hidden">
                        <div class="absolute top-0 left-1/3 w-96 h-96 bg-primary/[0.04] rounded-full blur-[100px]"></div>
                    </div>
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-primary/40"></div>
                            <span class="text-[11px] font-bold text-primary tracking-[0.22em] uppercase">Metodología</span>
                        </div>
                        <h2 class="font-serif text-3xl md:text-4xl text-on-surface mb-12 md:mb-16">
                            Cómo <em class="not-italic text-primary">trabajamos</em>
                        </h2>

                        <div class="grid md:grid-cols-3 gap-0 md:divide-x divide-outline-variant/15">
                            <div
                                v-for="(paso, i) in pasos"
                                :key="paso.n"
                                class="group px-0 md:px-10 first:pl-0 last:pr-0 pb-10 md:pb-0"
                                :class="{ 'border-b border-outline-variant/15 md:border-b-0 mb-10 md:mb-0': i < 2 }"
                            >
                                <!-- Número grande decorativo -->
                                <div class="flex items-center gap-4 mb-6">
                                    <span class="font-serif text-5xl font-bold text-on-surface/[0.06] leading-none select-none tabular-nums">{{ paso.n }}</span>
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/15 group-hover:scale-105 transition-all duration-300">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="paso.svg"></svg>
                                    </div>
                                </div>
                                <h3 class="font-serif font-bold text-on-surface text-xl mb-3 group-hover:text-primary transition-colors duration-300">{{ paso.titulo }}</h3>
                                <p class="text-on-surface-variant text-sm leading-relaxed">{{ paso.desc }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ═══════════════════════════════════════════
                     CONTACTO + FORMULARIO  — 2 columnas
                ═══════════════════════════════════════════ -->
                <section id="solicitar" class="py-8 md:py-24 bg-surface relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/[0.03] rounded-full blur-[120px] pointer-events-none -translate-y-1/3 translate-x-1/4"></div>

                    <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                        <!-- Label + Título -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-px w-8 bg-primary/40"></div>
                            <span class="text-[11px] font-bold text-primary tracking-[0.22em] uppercase">Contáctanos</span>
                        </div>
                        <h2 class="font-serif text-3xl md:text-4xl text-on-surface mb-5 md:mb-12">
                            Envíanos un <em class="not-italic text-primary">mensaje</em>
                        </h2>

                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16 items-start">

                            <!-- Col info: 2/5 (oculta en mobile, ya se muestra arriba compacta) -->
                            <div class="hidden lg:block lg:col-span-2 space-y-4">
                                <!-- Tarjeta ubicación -->
                                <div class="flex items-start gap-4 p-5 rounded-2xl bg-surface-container border border-outline-variant/15 hover:border-primary/20 transition-colors group">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/15 transition-colors">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-1">Dirección</p>
                                        <p class="text-sm font-semibold text-on-surface">{{ heroAddress }}</p>
                                    </div>
                                </div>

                                <!-- Tarjeta email -->
                                <div class="flex items-start gap-4 p-5 rounded-2xl bg-surface-container border border-outline-variant/15 hover:border-primary/20 transition-colors group">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/15 transition-colors">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-1">Correo electrónico</p>
                                        <a :href="`mailto:${heroEmail}`" class="text-sm font-semibold text-primary hover:underline underline-offset-2">{{ heroEmail }}</a>
                                    </div>
                                </div>

                                <!-- Tarjeta WhatsApp -->
                                <div class="flex items-start gap-4 p-5 rounded-2xl bg-green-500/[0.06] border border-green-500/15 hover:border-green-500/25 transition-colors group">
                                    <div class="w-10 h-10 rounded-xl bg-green-500/15 flex items-center justify-center flex-shrink-0 group-hover:bg-green-500/25 transition-colors">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-1">¿Prefieres hablar directo?</p>
                                        <a :href="whatsappLink" target="_blank" rel="noopener" class="text-sm font-bold text-green-600 hover:underline underline-offset-2">Escríbenos por WhatsApp →</a>
                                    </div>
                                </div>

                                <p class="text-xs text-on-surface-variant/40 leading-relaxed px-1 pt-2">
                                    Respondemos en un máximo de <strong class="text-on-surface-variant/60">24 horas hábiles</strong>. Tus datos son completamente confidenciales.
                                </p>
                            </div>

                            <!-- Col formulario: 3/5 -->
                            <div class="lg:col-span-3">
                                <!-- Flash éxito -->
                                <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                                    <div v-if="flashSuccess" class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-start gap-3">
                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-sm text-green-700 font-semibold">{{ flashSuccess }}</p>
                                    </div>
                                </transition>

                                <form @submit.prevent="submitForm" class="space-y-4">
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Nombre completo *"
                                        class="w-full px-4 py-3.5 rounded-xl bg-surface-container text-on-surface placeholder:text-on-surface-variant/35 text-sm focus:outline-none transition-all"
                                        :class="form.errors.name ? 'border-2 border-red-400' : 'border border-outline-variant/20 focus:border-primary/40 focus:ring-1 focus:ring-primary/20'"
                                    />
                                    <p v-if="form.errors.name" class="text-xs text-red-500 -mt-2 pl-1">{{ form.errors.name }}</p>

                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <input
                                                v-model="form.email"
                                                type="email"
                                                placeholder="Correo electrónico *"
                                                class="w-full px-4 py-3.5 rounded-xl bg-surface-container text-on-surface placeholder:text-on-surface-variant/35 text-sm focus:outline-none transition-all"
                                                :class="form.errors.email ? 'border-2 border-red-400' : 'border border-outline-variant/20 focus:border-primary/40 focus:ring-1 focus:ring-primary/20'"
                                            />
                                            <p v-if="form.errors.email" class="text-xs text-red-500 mt-1 pl-1">{{ form.errors.email }}</p>
                                        </div>
                                        <input
                                            v-model="form.phone"
                                            type="tel"
                                            placeholder="Teléfono / WhatsApp"
                                            class="w-full px-4 py-3.5 rounded-xl bg-surface-container border border-outline-variant/20 text-on-surface placeholder:text-on-surface-variant/35 text-sm focus:outline-none focus:border-primary/40 focus:ring-1 focus:ring-primary/20 transition-all"
                                        />
                                    </div>

                                    <input
                                        v-model="form.company"
                                        type="text"
                                        placeholder="Empresa o institución"
                                        class="w-full px-4 py-3.5 rounded-xl bg-surface-container border border-outline-variant/20 text-on-surface placeholder:text-on-surface-variant/35 text-sm focus:outline-none focus:border-primary/40 focus:ring-1 focus:ring-primary/20 transition-all"
                                    />

                                    <div class="relative">
                                        <select
                                            v-model="form.area"
                                            class="w-full px-4 py-3.5 rounded-xl bg-surface-container text-sm focus:outline-none transition-all appearance-none pr-10"
                                            :class="[
                                                form.area ? 'text-on-surface' : 'text-on-surface-variant/35',
                                                form.errors.area ? 'border-2 border-red-400' : 'border border-outline-variant/20 focus:border-primary/40 focus:ring-1 focus:ring-primary/20'
                                            ]"
                                        >
                                            <option value="" disabled>Área de consultoría de interés *</option>
                                            <option v-for="area in areas" :key="area" :value="area" class="text-on-surface bg-surface">{{ area }}</option>
                                        </select>
                                        <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-variant/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        <p v-if="form.errors.area" class="text-xs text-red-500 mt-1 pl-1">{{ form.errors.area }}</p>
                                    </div>

                                    <textarea
                                        v-model="form.message"
                                        rows="4"
                                        placeholder="Tu mensaje *"
                                        class="w-full px-4 py-3.5 rounded-xl bg-surface-container text-on-surface placeholder:text-on-surface-variant/35 text-sm focus:outline-none transition-all resize-none"
                                        :class="form.errors.message ? 'border-2 border-red-400' : 'border border-outline-variant/20 focus:border-primary/40 focus:ring-1 focus:ring-primary/20'"
                                    ></textarea>
                                    <p v-if="form.errors.message" class="text-xs text-red-500 -mt-2 pl-1">{{ form.errors.message }}</p>

                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="w-full flex items-center justify-center gap-2.5 px-6 py-4 rounded-xl font-bold text-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl disabled:opacity-60 disabled:translate-y-0 disabled:cursor-not-allowed"
                                        style="background-color: var(--md-sys-color-primary); color: var(--md-sys-color-on-primary);"
                                    >
                                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        {{ form.processing ? 'Enviando...' : 'ENVIAR MENSAJE' }}
                                    </button>
                                </form>

                                <!-- Contacto rápido mobile (debajo del formulario) -->
                                <div class="md:hidden flex flex-col gap-2 mt-4">
                                    <p class="text-xs text-on-surface-variant/50 text-center mb-1">¿Prefieres contactar directo?</p>
                                    <a
                                        :href="whatsappLink"
                                        target="_blank"
                                        rel="noopener"
                                        class="flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-green-500 text-white font-bold shadow-md shadow-green-500/20"
                                    >
                                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        <span class="flex-1 text-sm">Escríbenos por WhatsApp</span>
                                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </a>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a :href="`mailto:${heroEmail}`" class="flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-surface-container border border-outline-variant/20">
                                            <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <span class="text-xs font-medium text-on-surface truncate">{{ heroEmail }}</span>
                                        </a>
                                        <div class="flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-surface-container border border-outline-variant/20">
                                            <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="text-xs font-medium text-on-surface">Trujillo, Perú</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </main>

            <!-- Footer -->
            <footer v-if="!isDashboard" class="bg-surface-container-low border-t border-outline-variant/10">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 py-10">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <img src="/images/empresa/IEE-Logo02.png" alt="IEE" class="h-9 w-auto" />
                        <div class="flex gap-6 text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">
                            <Link :href="route('cursos.index')" class="hover:text-primary transition-colors">Cursos</Link>
                            <Link :href="route('publicaciones.index')" class="hover:text-primary transition-colors">Publicaciones</Link>
                            <Link :href="route('masterclass.index')" class="hover:text-primary transition-colors">Masterclass</Link>
                        </div>
                        <p class="text-[10px] text-on-surface-variant/40">© {{ new Date().getFullYear() }} Instituto de Economía y Empresa · Trujillo, Perú</p>
                    </div>
                </div>
            </footer>
        </div>
    </component>
</template>

<style scoped>
@keyframes heroZoom {
    from { transform: scale(1.05); }
    to   { transform: scale(1); }
}
</style>
