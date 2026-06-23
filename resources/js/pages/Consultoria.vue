<script setup lang="ts">
import BrandLogo from '@/components/BrandLogo.vue';
import ConsultoriaContact from '@/components/consultoria/ConsultoriaContact.vue';
import ConsultoriaHero from '@/components/consultoria/ConsultoriaHero.vue';
import ConsultoriaProcess from '@/components/consultoria/ConsultoriaProcess.vue';
import ConsultoriaServices from '@/components/consultoria/ConsultoriaServices.vue';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import type { ConsultoriaPaso, ConsultoriaProps } from '@/types/consultoria';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<ConsultoriaProps>();

const page = usePage<SharedData>();
const siteSettings = computed(
    () =>
        page.props.site_settings as
            | {
                  whatsapp_sales?: string;
                  contact_email?: string;
                  contact_address?: string;
              }
            | undefined,
);

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Consultoría', href: '#' },
];

const heroWhatsapp = computed(() => props.banner?.whatsapp_number || siteSettings.value?.whatsapp_sales || '51959166911');
const heroEmail = computed(() => props.banner?.contact_email || siteSettings.value?.contact_email || 'info@iee.edu.pe');
const heroAddress = computed(() => props.banner?.contact_address || siteSettings.value?.contact_address || 'Trujillo, La Libertad — Perú');
const whatsappLink = computed(() => `https://wa.me/${heroWhatsapp.value}?text=Hola%2C%20me%20interesa%20una%20consultor%C3%ADa%20con%20el%20IEE`);

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

const pasos: ConsultoriaPaso[] = [
    {
        n: '01',
        titulo: 'Diagnóstico',
        desc: 'Analizamos tu situación actual, identificamos brechas y oportunidades con rigor técnico y metodológico.',
        svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
    },
    {
        n: '02',
        titulo: 'Propuesta técnica',
        desc: 'Diseñamos una solución a medida, basada en evidencia y alineada con los objetivos estratégicos de tu organización.',
        svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    },
    {
        n: '03',
        titulo: 'Ejecución y resultados',
        desc: 'Acompañamos la implementación, medimos el impacto real y garantizamos la sostenibilidad de los cambios.',
        svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>',
    },
];
</script>

<template>
    <Head title="Consultoría Especializada — IEE" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['min-h-screen font-sans', !isDashboard ? 'bg-surface text-on-surface' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="[!isDashboard ? 'pt-20' : 'pt-0']">
                <ConsultoriaHero :banner="banner" />
                <ConsultoriaServices :areas="areas" />
                <ConsultoriaProcess :pasos="pasos" />
                <ConsultoriaContact :areas="areas" :hero-email="heroEmail" :hero-address="heroAddress" :whatsapp-link="whatsappLink" />
            </main>

            <footer v-if="!isDashboard" class="border-t border-outline-variant/10 bg-surface-container-low">
                <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
                    <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
                        <BrandLogo imageClass="h-9 w-auto" />
                        <div class="flex gap-6 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">
                            <Link :href="route('cursos.index')" class="transition-colors hover:text-primary">Cursos</Link>
                            <Link :href="route('publicaciones.index')" class="transition-colors hover:text-primary">Publicaciones</Link>
                            <Link :href="route('masterclass.index')" class="transition-colors hover:text-primary">Masterclass</Link>
                        </div>
                        <p class="text-[10px] text-on-surface-variant/40">
                            © {{ new Date().getFullYear() }} Instituto de Economía y Empresa · Trujillo, Perú
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </component>
</template>

<style scoped>
@keyframes heroZoom {
    from {
        transform: scale(1.05);
    }
    to {
        transform: scale(1);
    }
}
</style>
