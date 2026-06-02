<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Compass, Lock, ServerCrash, HelpCircle, ArrowLeft, Home } from 'lucide-vue-next';

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
            icon: Lock
        },
        404: {
            title: 'Página no Encontrada',
            subtitle: 'Error de Navegación 404',
            desc: 'La cátedra o recurso que intentas buscar no se encuentra disponible. Puede que la ruta sea incorrecta o haya cambiado de ubicación.',
            icon: Compass
        },
        500: {
            title: 'Error de Servidor',
            subtitle: 'Inconsistencia Técnica 500',
            desc: 'Nuestro sistema académico ha experimentado una inconsistencia interna. Nuestro equipo técnico ya ha sido notificado.',
            icon: ServerCrash
        },
        503: {
            title: 'Cátedra en Mantenimiento',
            subtitle: 'Servicio Temporal 503',
            desc: 'Estamos realizando labores de optimización y mantenimiento en el aula virtual. Volveremos a estar en línea en unos minutos.',
            icon: HelpCircle
        }
    };
    
    return details[code] || {
        title: 'Error Inesperado',
        subtitle: `Código de Error ${code}`,
        desc: 'Ha ocurrido un error inesperado al procesar la solicitud académica.',
        icon: HelpCircle
    };
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
    
    <div class="min-h-screen bg-background text-on-background flex items-center justify-center p-6 md:p-12 relative overflow-hidden font-sans selection:bg-primary/20 selection:text-primary">
        <!-- Elegant grid background pattern -->
        <div class="absolute inset-0 bg-[radial-gradient(#57572a_0.5px,transparent_0.5px)] [background-size:16px_16px] opacity-[0.03] pointer-events-none"></div>
        
        <!-- Ambient radial glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="w-full max-w-xl text-center relative z-10 space-y-8 animate-in fade-in slide-in-from-bottom-6 duration-700">
            <!-- Giant elegant status code -->
            <div class="relative inline-block select-none">
                <span class="text-8xl md:text-9xl font-serif font-bold italic tracking-tighter text-primary/10 select-none">
                    {{ status }}
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-white dark:bg-surface border border-outline-variant/10 rounded-full flex items-center justify-center shadow-lg transform rotate-6 animate-pulse">
                        <component :is="errorDetails.icon" class="w-8 h-8 md:w-10 md:h-10 text-primary" />
                    </div>
                </div>
            </div>

            <!-- Double-bordered elegant card -->
            <div class="bg-white dark:bg-surface border-2 border-outline-variant/15 border-double p-8 md:p-10 rounded-[3rem] shadow-xl space-y-6 relative overflow-hidden">
                <div class="absolute top-3 left-3 w-6 h-6 border-t-2 border-l-2 border-primary/20"></div>
                <div class="absolute top-3 right-3 w-6 h-6 border-t-2 border-r-2 border-primary/20"></div>
                <div class="absolute bottom-3 left-3 w-6 h-6 border-b-2 border-l-2 border-primary/20"></div>
                <div class="absolute bottom-3 right-3 w-6 h-6 border-b-2 border-r-2 border-primary/20"></div>

                <div class="space-y-2">
                    <span class="text-[10px] md:text-[11px] font-black uppercase tracking-[0.25em] text-[#8c7a56]">
                        {{ errorDetails.subtitle }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-serif font-black text-on-background italic">
                        {{ errorDetails.title }}
                    </h1>
                </div>

                <p class="text-xs md:text-sm text-on-surface-variant leading-relaxed max-w-sm mx-auto font-medium font-serif italic">
                    {{ errorDetails.desc }}
                </p>

                <!-- Navigation buttons inside card -->
                <div class="pt-4 flex flex-col sm:flex-row gap-4 justify-center">
                    <button 
                        @click="goBack"
                        class="w-full sm:w-auto px-6 py-3 bg-surface-container-low dark:bg-surface-2 text-on-surface-variant hover:text-primary border border-outline-variant/20 rounded-xl hover:bg-white dark:hover:bg-surface-3 hover:border-primary/20 hover:scale-[1.02] active:scale-95 transition-all text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Regresar
                    </button>
                    
                    <Link 
                        href="/dashboard"
                        class="w-full sm:w-auto px-6 py-3 bg-primary text-white hover:bg-on-background hover:scale-[1.02] active:scale-95 transition-all shadow-md hover:shadow-lg text-xs font-bold uppercase tracking-wider rounded-xl flex items-center justify-center gap-2"
                    >
                        <Home class="w-4 h-4" />
                        Ir al Dashboard
                    </Link>
                </div>
            </div>
            
            <!-- Branding footer -->
            <p class="text-[9px] text-on-surface-variant/40 font-bold uppercase tracking-widest">
                Instituto de Economía y Empresa © {{ new Date().getFullYear() }}
            </p>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% { transform: scale(1) rotate(6deg); }
    50% { transform: scale(1.05) rotate(9deg); }
}
.animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
