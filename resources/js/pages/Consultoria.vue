<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navigation from '../components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { 
    BarChart3, GitMerge, TrendingUp, Cpu, ShieldCheck, 
    DollarSign, Layers, Handshake, Map, Leaf, 
    LineChart, FileText, ArrowRight, CheckCircle2,
    Building2, Users2, Briefcase, GraduationCap
} from 'lucide-vue-next';

const props = defineProps<{
    isDashboard?: boolean;
    banner?: {
        heading: string;
        subheading: string;
        image_path: string | null;
        button_text: string | null;
        button_link: string | null;
        position: string;
    } | null;
}>();

// Valores dinámicos del banner (BD) con fallback al contenido original
const heroHeading = computed(() => props.banner?.heading || 'Arquitectura Estratégica para Decisiones Críticas');
const heroSubheading = computed(() => props.banner?.subheading || 'En IEE, transformamos desafíos complejos en ventajas competitivas. Nuestra consultoría técnica especializada combina rigor académico con pragmatismo empresarial.');
const heroImage = computed(() => props.banner?.image_path || '/images/landing/consultoria_hero.png');
const heroButtonText = computed(() => props.banner?.button_text || 'Agendar Consultoría');
const heroButtonLink = computed(() => props.banner?.button_link || '#');

const services = [
    { 
        title: 'Planeamiento empresarial y de negocios', 
        description: 'Estrategias a medida para el crecimiento sostenible y posicionamiento competitivo en el mercado actual.',
        icon: BarChart3,
        color: 'bg-blue-500/10 text-blue-600'
    },
    { 
        title: 'Reorganización empresarial e institucional', 
        description: 'Optimización de estructuras organizativas para mejorar la eficiencia operativa y la agilidad institucional.',
        icon: GitMerge,
        color: 'bg-purple-500/10 text-purple-600'
    },
    { 
        title: 'Valorización empresarial', 
        description: 'Análisis profundo y técnico del valor real de su organización para tomas de decisiones estratégicas.',
        icon: TrendingUp,
        color: 'bg-emerald-500/10 text-emerald-600'
    },
    { 
        title: 'Transformación digital', 
        description: 'Integración de tecnologías de vanguardia para evolucionar su modelo de negocio hacia la era digital.',
        icon: Cpu,
        color: 'bg-orange-500/10 text-orange-600'
    },
    { 
        title: 'Implementación de sistemas ISO', 
        description: 'Acompañamiento experto en la estandarización y certificación de procesos bajo normas internacionales.',
        icon: ShieldCheck,
        color: 'bg-cyan-500/10 text-cyan-600'
    },
    { 
        title: 'Oportunidades de inversión y financiamiento N/I', 
        description: 'Identificación y estructuración de fuentes de capital nacionales e internacionales.',
        icon: DollarSign,
        color: 'bg-amber-500/10 text-amber-600'
    },
    { 
        title: 'Estructuración de Proyectos: PA, APP, OxI', 
        description: 'Desarrollo técnico de proyectos bajo modalidades de Asociaciones Público Privadas y Obras por Impuestos.',
        icon: Layers,
        color: 'bg-indigo-500/10 text-indigo-600'
    },
    { 
        title: 'Asistencia en negociación societaria y apalancamiento AO/AF', 
        description: 'Apoyo estratégico en acuerdos corporativos y optimización de estructuras de capital.',
        icon: Handshake,
        color: 'bg-rose-500/10 text-rose-600'
    },
    { 
        title: 'Planes: PDM, DPU, PAT y DET, MUS', 
        description: 'Elaboración de instrumentos de planificación territorial y desarrollo urbano de alta precisión.',
        icon: Map,
        color: 'bg-lime-500/10 text-lime-600'
    },
    { 
        title: 'Estudios de impacto ambiental y vial', 
        description: 'Evaluaciones técnicas rigurosas para garantizar la viabilidad y sostenibilidad de sus proyectos.',
        icon: Leaf,
        color: 'bg-green-500/10 text-green-600'
    },
    { 
        title: 'Estudios de medición de impacto socioeconómico y financiero', 
        description: 'Cuantificación precisa del valor social y económico generado por inversiones e intervenciones.',
        icon: LineChart,
        color: 'bg-violet-500/10 text-violet-600'
    },
    { 
        title: 'Informes de dinámica sectorial y rubros de actividad', 
        description: 'Inteligencia de mercado especializada para comprender las tendencias y riesgos de su sector.',
        icon: FileText,
        color: 'bg-slate-500/10 text-slate-600'
    },
];

const stats = [
    { label: 'Proyectos Entregados', value: '150+', icon: Briefcase },
    { label: 'Empresas Asesoradas', value: '80+', icon: Building2 },
    { label: 'Especialistas Senior', value: '25+', icon: Users2 },
    { label: 'Sectores Atendidos', value: '12', icon: GraduationCap },
];

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Consultoría Especializada', href: '#' },
];
</script>

<template>
    <Head title="Consultoría Estratégica - IEE" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['min-h-screen font-sans selection:bg-primary/20 selection:text-primary', !isDashboard ? 'bg-surface' : 'bg-transparent shadow-none']">
            <Navigation v-if="!isDashboard" />

            <main :class="[!isDashboard ? 'pt-20' : 'pt-0']">
            <!-- Hero Section -->
            <section class="relative py-20 lg:py-32 overflow-hidden">
                <!-- Background Decor -->
                <div class="absolute inset-0 z-0">
                    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>
                    <div class="absolute top-1/2 -right-24 w-80 h-80 bg-secondary/5 rounded-full blur-[80px]"></div>
                </div>

                <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <div class="space-y-8">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                </span>
                                Soluciones de Alto Impacto
                            </div>
                            
                            <h1 class="text-4xl lg:text-6xl font-serif leading-[1.1] text-on-surface">
                                {{ heroHeading }}
                            </h1>
                            
                            <p class="text-lg lg:text-xl text-on-surface-variant leading-relaxed max-w-xl">
                                {{ heroSubheading }}
                            </p>

                            <div class="flex flex-wrap gap-4">
                                <a :href="heroButtonLink" class="px-8 py-4 bg-primary text-on-primary rounded-xl font-bold shadow-lg shadow-primary/20 hover:shadow-xl hover:bg-primary/90 transition-all flex items-center gap-2 group">
                                    {{ heroButtonText }}
                                    <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                                </a>
                                <a href="#servicios" class="px-8 py-4 bg-surface border border-outline-variant rounded-xl font-bold hover:bg-surface-container-low transition-all">
                                    Ver Portafolio
                                </a>
                            </div>

                            <div class="flex items-center gap-6 pt-4 border-t border-outline-variant/30 max-w-md">
                                <div class="flex -space-x-3">
                                    <div v-for="i in 4" :key="i" class="w-10 h-10 rounded-full border-2 border-surface bg-surface-container-high flex items-center justify-center overflow-hidden">
                                        <img :src="`https://i.pravatar.cc/100?img=${i+10}`" alt="Avatar" class="w-full h-full object-cover grayscale" />
                                    </div>
                                </div>
                                <div class="text-sm">
                                    <p class="font-bold text-on-surface">+50 Directivos</p>
                                    <p class="text-on-surface-variant">Confían en nuestra visión</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="relative z-10 rounded-[2.5rem] overflow-hidden border border-outline-variant shadow-2xl skew-x-1 -rotate-1 hover:rotate-0 hover:skew-x-0 transition-transform duration-700">
                                <img :src="heroImage" alt="Consultoría Premium" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#0c2b2c]/80 to-transparent flex items-bottom p-8">
                                    <div class="mt-auto">
                                        <p class="text-on-primary text-xl font-serif italic">"Excelencia técnica con visión de futuro."</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Small accent card -->
                            <div class="absolute -bottom-6 -left-6 z-20 bg-surface-container p-6 rounded-2xl shadow-xl border border-outline-variant/20 animate-bounce-slow">
                                <TrendingUp class="w-8 h-8 text-primary mb-2" />
                                <p class="text-2xl font-bold text-on-surface">98%</p>
                                <p class="text-xs text-on-surface-variant font-medium">Índice de Retorno</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="py-12 bg-surface-container-low border-y border-outline-variant/10">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-for="stat in stats" :key="stat.label" class="flex flex-col items-center text-center space-y-2">
                            <div class="p-3 bg-surface rounded-xl border border-outline-variant/20 shadow-sm">
                                <component :is="stat.icon" class="w-6 h-6 text-primary/60" />
                            </div>
                            <div>
                                <p class="text-3xl font-bold text-on-surface tracking-tight">{{ stat.value }}</p>
                                <p class="text-xs text-on-surface-variant font-bold uppercase tracking-widest">{{ stat.label }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Services Section -->
            <section id="servicios" class="py-20 lg:py-32 bg-surface relative">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="max-w-3xl mb-20">
                        <h2 class="text-primary text-sm font-bold uppercase tracking-[0.2em] mb-4">Nuestra Expertise</h2>
                        <h3 class="text-4xl lg:text-5xl font-serif text-on-surface leading-tight">
                            Soluciones Multidisciplinarias para un <span class="italic">Entorno Global</span>
                        </h3>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        <div 
                            v-for="(service, index) in services" 
                            :key="index"
                            class="group p-8 rounded-3xl bg-surface-container-low border border-outline-variant/30 hover:border-primary/40 hover:bg-surface-container transition-all duration-500 hover:shadow-2xl hover:shadow-primary/5 cursor-default"
                        >
                            <div :class="[
                                'w-14 h-14 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-500',
                                service.color
                            ]">
                                <component :is="service.icon" class="w-7 h-7" />
                            </div>
                            
                            <h4 class="text-xl font-serif font-bold text-on-surface mb-4 group-hover:text-primary transition-colors leading-tight">
                                {{ service.title }}
                            </h4>
                            
                            <p class="text-on-surface-variant text-sm leading-relaxed mb-8 opacity-80 group-hover:opacity-100">
                                {{ service.description }}
                            </p>

                            <div class="flex items-center gap-2 text-xs font-bold text-primary opacity-0 group-hover:opacity-100 transition-all">
                                SOLICITAR INFORMACIÓN
                                <ArrowRight class="w-3 h-3" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Methodology Section -->
            <section class="py-20 lg:py-32 bg-surface-container-low overflow-hidden relative">
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-primary/[0.06] rounded-full blur-[120px]"></div>
                    <div class="absolute -bottom-20 -right-20 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px]"></div>
                </div>

                <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-20 items-center">
                        <div>
                            <h2 class="text-primary text-sm font-bold uppercase tracking-[0.2em] mb-6">Metodología de Trabajo</h2>
                            <h3 class="text-4xl lg:text-5xl font-serif text-on-surface mb-12">Cómo transformamos su visión en resultados tangibles</h3>
                            
                            <div class="space-y-8">
                                <div v-for="(step, i) in [
                                    { t: 'Diagnóstico de Situación', d: 'Análisis profundo de la problemática y identificación de cuellos de botella.' },
                                    { t: 'Arquitectura de Solución', d: 'Diseño técnico de la estrategia bajo marcos normativos y competitivos.' },
                                    { t: 'Ejecución y Monitoreo', d: 'Implementación guiada con métricas clave de desempeño (KPIs).' }
                                ]" :key="i" class="flex gap-6">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full border border-primary/40 flex items-center justify-center font-serif text-xl italic text-primary">
                                        0{{ i + 1 }}
                                    </div>
                                    <div class="space-y-2">
                                        <h4 class="text-xl font-serif font-bold text-on-surface">{{ step.t }}</h4>
                                        <p class="text-on-surface-variant text-sm leading-relaxed">{{ step.d }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-surface-container rounded-2xl p-10 border border-outline-variant/15 shadow-xl">
                            <h4 class="text-2xl font-serif font-bold mb-8 italic text-on-surface">Compromiso con la Calidad</h4>
                            <div class="space-y-6">
                                <div v-for="point in [
                                    'Rigor metodológico académico',
                                    'Confidencialidad absoluta',
                                    'Plazos óptimos de entrega',
                                    'Asistencia post-consultoría'
                                ]" :key="point" class="flex items-center gap-4">
                                    <div class="p-1 bg-primary rounded-full">
                                        <CheckCircle2 class="w-4 h-4 text-on-primary" />
                                    </div>
                                    <span class="text-on-surface-variant font-medium">{{ point }}</span>
                                </div>
                            </div>

                            <button class="w-full mt-12 py-4 bg-primary text-on-primary rounded-xl font-bold hover:opacity-90 hover:shadow-lg transition-all">
                                Iniciar Proceso de Consultoría
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Final CTA -->
            <section class="py-20 lg:py-32 bg-surface">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
                    <div class="max-w-3xl mx-auto space-y-8">
                        <Building2 class="w-16 h-16 text-primary/40 mx-auto" />
                        <h2 class="text-4xl lg:text-6xl font-serif text-on-surface">¿Listo para escalar su <span class="italic text-primary">organización?</span></h2>
                        <p class="text-on-surface-variant text-lg">Hablemos sobre sus objetivos y cómo IEE puede ayudarle a alcanzarlos.</p>
                        
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <button class="px-10 py-5 bg-primary text-on-primary rounded-2xl font-bold shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                                Agendar llamada técnica
                            </button>
                            <button class="px-10 py-5 bg-surface border border-outline-variant rounded-2xl font-bold hover:bg-surface-container transition-all">
                                Contactar vía WhatsApp
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    
        <!-- Footer -->
        <footer v-if="!isDashboard" class="bg-surface-container-low border-t border-outline-variant/10">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex items-center gap-3">
                        <img src="/images/empresa/IEE-Logo02.png" alt="IEE Logo" class="h-10 w-auto" />
                        <div class="h-6 w-[1px] bg-outline-variant/30 hidden md:block"></div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] hidden md:block">
                            Consultoría Especializada
                        </p>
                    </div>
                    
                    <div class="flex gap-8 text-[11px] font-bold text-on-surface-variant uppercase tracking-widest">
                        <Link :href="route('cursos.index')" class="hover:text-primary transition-colors">Cursos</Link>
                        <Link :href="route('publicaciones.index')" class="hover:text-primary transition-colors">Publicaciones</Link>
                        <Link :href="route('masterclass.index')" class="hover:text-primary transition-colors">Masterclass</Link>
                    </div>

                    <p class="text-[10px] text-on-surface-variant/40 font-medium">
                        © 2024 IEE. Excelencia en Formación y Consultoría.
                    </p>
                </div>
            </div>
        </footer>
        </div>
    </component>
</template>

<style scoped>
.animate-bounce-slow {
    animation: bounce 3s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(-5%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}
</style>
