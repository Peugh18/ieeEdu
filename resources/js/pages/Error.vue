<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Compass, HelpCircle, Home, Lock, ServerCrash } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    status: number;
}>();

const errorDetails = computed(() => {
    const code = props.status;

    const details: Record<number, { title: string; subtitle: string; desc: string; icon: any }> = {
        403: {
            title: 'Acceso Restringido',
            subtitle: 'Error de Autorización 403',
            desc: 'Lo sentimos, no cuentas con las credenciales académicas necesarias para acceder a este módulo privado o recurso.',
            icon: Lock,
        },
        404: {
            title: 'Página no Encontrada',
            subtitle: 'Error de Navegación 404',
            desc: 'La cátedra o recurso que intentas buscar no se encuentra disponible. Puede que la ruta sea incorrecta o haya cambiado de ubicación.',
            icon: Compass,
        },
        500: {
            title: 'Error de Servidor',
            subtitle: 'Inconsistencia Técnica 500',
            desc: 'Nuestro sistema académico ha experimentado una inconsistencia interna. Nuestro equipo técnico ya ha sido notificado.',
            icon: ServerCrash,
        },
        503: {
            title: 'Cátedra en Mantenimiento',
            subtitle: 'Servicio Temporal 503',
            desc: 'Estamos realizando labores de optimización y mantenimiento en el aula virtual. Volveremos a estar en línea en unos minutos.',
            icon: HelpCircle,
        },
    };

    return (
        details[code] || {
            title: 'Error Inesperado',
            subtitle: `Código de Error ${code}`,
            desc: 'Ha ocurrido un error inesperado al procesar la solicitud académica.',
            icon: HelpCircle,
        }
    );
});

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/dashboard';
    }
}
</script>

<template>
    <Head :title="`${status} - ${errorDetails.title}`" />

    <div
        class="relative flex min-h-screen items-center justify-center overflow-hidden bg-background p-6 font-sans text-on-background selection:bg-primary/20 selection:text-primary md:p-12"
    >
        <!-- Elegant grid background pattern -->
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(#57572a_0.5px,transparent_0.5px)] opacity-[0.03] [background-size:16px_16px]"
        ></div>

        <!-- Ambient radial glow -->
        <div
            class="pointer-events-none absolute left-1/2 top-1/2 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-primary/5 blur-[80px]"
        ></div>

        <div class="relative z-10 w-full max-w-xl space-y-8 text-center duration-700 animate-in fade-in slide-in-from-bottom-6">
            <!-- Giant elegant status code -->
            <div class="relative inline-block select-none">
                <span class="select-none font-serif text-8xl font-bold italic tracking-tighter text-primary/10 md:text-9xl">
                    {{ status }}
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div
                        class="flex h-16 w-16 rotate-6 transform animate-pulse items-center justify-center rounded-full border border-outline-variant/10 bg-white shadow-lg dark:bg-surface md:h-20 md:w-20"
                    >
                        <component :is="errorDetails.icon" class="h-8 w-8 text-primary md:h-10 md:w-10" />
                    </div>
                </div>
            </div>

            <!-- Double-bordered elegant card -->
            <div
                class="relative space-y-6 overflow-hidden rounded-[3rem] border-2 border-double border-outline-variant/15 bg-white p-8 shadow-xl dark:bg-surface md:p-10"
            >
                <div class="absolute left-3 top-3 h-6 w-6 border-l-2 border-t-2 border-primary/20"></div>
                <div class="absolute right-3 top-3 h-6 w-6 border-r-2 border-t-2 border-primary/20"></div>
                <div class="absolute bottom-3 left-3 h-6 w-6 border-b-2 border-l-2 border-primary/20"></div>
                <div class="absolute bottom-3 right-3 h-6 w-6 border-b-2 border-r-2 border-primary/20"></div>

                <div class="space-y-2">
                    <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#8c7a56] md:text-[11px]">
                        {{ errorDetails.subtitle }}
                    </span>
                    <h1 class="font-serif text-2xl font-black italic text-on-background md:text-3xl">
                        {{ errorDetails.title }}
                    </h1>
                </div>

                <p class="mx-auto max-w-sm font-serif text-xs font-medium italic leading-relaxed text-on-surface-variant md:text-sm">
                    {{ errorDetails.desc }}
                </p>

                <!-- Navigation buttons inside card -->
                <div class="flex flex-col justify-center gap-4 pt-4 sm:flex-row">
                    <button
                        @click="goBack"
                        class="dark:bg-surface-2 dark:hover:bg-surface-3 flex w-full items-center justify-center gap-2 rounded-xl border border-outline-variant/20 bg-surface-container-low px-6 py-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant transition-all hover:scale-[1.02] hover:border-primary/20 hover:bg-white hover:text-primary active:scale-95 sm:w-auto"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Regresar
                    </button>

                    <Link
                        href="/dashboard"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-md transition-all hover:scale-[1.02] hover:bg-on-background hover:shadow-lg active:scale-95 sm:w-auto"
                    >
                        <Home class="h-4 w-4" />
                        Ir al Dashboard
                    </Link>
                </div>
            </div>

            <!-- Branding footer -->
            <p class="text-[9px] font-bold uppercase tracking-widest text-on-surface-variant/40">
                Instituto de Economía y Empresa © {{ new Date().getFullYear() }}
            </p>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%,
    100% {
        transform: scale(1) rotate(6deg);
    }
    50% {
        transform: scale(1.05) rotate(9deg);
    }
}
.animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
