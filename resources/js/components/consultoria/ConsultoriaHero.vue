<script setup lang="ts">
import type { ConsultoriaBanner } from '@/types/consultoria';
import { computed } from 'vue';

const props = defineProps<{
    banner?: ConsultoriaBanner | null;
}>();

const heroHeading = computed(() => props.banner?.heading || 'Consultoría especializada para el sector empresarial y público');
const heroSubtitle = computed(() => props.banner?.subheading || 'Soluciones prácticas, técnicas y basadas en evidencia para impulsar la gestión y competitividad de tu organización.');
const heroBg = computed(() => props.banner?.image_path || '/images/landing/consultoria_hero.jpg');
const heroWhatsapp = computed(() => props.banner?.whatsapp_number || '51959166911');
const heroEmail = computed(() => props.banner?.contact_email || 'info@iee.edu.pe');
const heroAddress = computed(() => props.banner?.contact_address || 'Trujillo, La Libertad — Perú');
const whatsappLink = computed(() => `https://wa.me/${heroWhatsapp.value}?text=Hola%2C%20me%20interesa%20una%20consultor%C3%ADa%20con%20el%20IEE`);
</script>

<template>
    <section class="md:hidden relative overflow-hidden bg-surface-container-low px-6 pt-6 pb-8">
        <div class="absolute top-0 right-0 w-48 h-48 bg-primary/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/4 pointer-events-none"></div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 border border-primary/20 mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
            <span class="text-[11px] font-bold text-primary uppercase tracking-widest">Consultoría IEE</span>
        </div>
        <h1 class="font-serif text-[1.75rem] leading-tight font-bold text-on-surface mb-2">
            ¿Necesitas asesoría<br/><span class="text-primary italic">especializada?</span>
        </h1>
        <p class="text-on-surface-variant text-sm mb-6">Trujillo, Perú · Respondemos en 24h</p>
        <div class="flex flex-col gap-3">
            <a :href="whatsappLink" target="_blank" rel="noopener" class="flex items-center gap-3 px-4 py-4 rounded-2xl bg-green-500 text-white font-bold shadow-lg shadow-green-500/20">
                <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <div class="flex-1"><p class="text-[11px] text-white/80 font-normal">Contáctanos ahora</p><p class="text-sm font-bold">WhatsApp</p></div>
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <div class="grid grid-cols-2 gap-3">
                <a :href="`mailto:${heroEmail}`" class="flex items-center gap-2.5 px-4 py-3.5 rounded-2xl bg-surface-container border border-outline-variant/20">
                    <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <div><p class="text-[10px] text-on-surface-variant">Email</p><p class="text-xs font-semibold text-on-surface truncate">Escribir</p></div>
                </a>
                <a href="#solicitar" class="flex items-center gap-2.5 px-4 py-3.5 rounded-2xl border border-primary/30 bg-primary/5">
                    <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div><p class="text-[10px] text-on-surface-variant">Formulario</p><p class="text-xs font-semibold text-primary">Solicitar</p></div>
                </a>
            </div>
        </div>
    </section>

    <section class="hidden md:block relative overflow-hidden w-full" style="aspect-ratio: 1920 / 600;">
        <img :src="heroBg" alt="IEE Consultoría" class="absolute inset-0 w-full h-full object-cover object-center scale-105 transition-transform duration-[8s] ease-out" style="animation: heroZoom 8s ease-out forwards;" />
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.3) 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 60%);"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,&lt;svg xmlns=%22http://www.w3.org/2000/svg%22 width=%224%22 height=%224%22&gt;&lt;rect width=%224%22 height=%224%22 fill=%22white%22/&gt;&lt;rect width=%221%22 height=%221%22 fill=%22black%22/&gt;&lt;/svg&gt;');"></div>

        <div class="relative z-10 h-full flex flex-col justify-end max-w-7xl mx-auto px-6 lg:px-8 pb-10 md:pb-14">
            <div class="flex items-center gap-2.5 mb-5">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-[0.18em] text-white/70 border border-white/15 backdrop-blur-sm" style="background: rgba(255,255,255,0.07);">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse flex-shrink-0"></span>
                    IEE · Desde 2009 · Trujillo, Perú
                </span>
            </div>
            <h1 class="font-serif text-3xl md:text-5xl lg:text-[3.25rem] text-white font-bold leading-[1.12] mb-4 max-w-3xl drop-shadow-2xl">{{ heroHeading }}</h1>
            <p class="text-white/65 text-sm md:text-base max-w-xl mb-8 leading-relaxed">{{ heroSubtitle }}</p>
            <div class="flex flex-wrap gap-3">
                <a :href="`mailto:${heroEmail}`" class="group flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all duration-300" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14); backdrop-filter: blur(8px);" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg class="w-4 h-4 flex-shrink-0" style="color: var(--md-sys-color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="text-white text-sm font-medium">{{ heroEmail }}</span>
                </a>
                <a :href="whatsappLink" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl transition-all duration-300" style="background: rgba(37,211,102,0.15); border: 1px solid rgba(37,211,102,0.25); backdrop-filter: blur(8px);" onmouseover="this.style.background='rgba(37,211,102,0.25)'" onmouseout="this.style.background='rgba(37,211,102,0.15)'">
                    <svg class="w-4 h-4 flex-shrink-0 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span class="text-white text-sm font-medium">WhatsApp</span>
                </a>
                <div class="w-px bg-white/10 self-stretch mx-1 hidden sm:block"></div>
                <div class="flex items-center gap-2 px-4 py-2.5 text-white/50 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ heroAddress }}
                </div>
                <a href="#solicitar" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl ml-auto sm:ml-0 bg-primary text-white">
                    Solicitar consultoría
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>
</template>
