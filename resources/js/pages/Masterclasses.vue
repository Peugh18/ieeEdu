<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    courses: any[];
    categories: any[];
    filters: { category?: string };
    isDashboard?: boolean;
}>();

const selectedCategory = ref(props.filters.category || 'Todas');

function applyFilters(category: string) {
    selectedCategory.value = category;
    const routeName = props.isDashboard ? 'student.explore.masterclasses' : 'masterclass.index';
    router.get(
        route(routeName),
        { category: category === 'Todas' ? '' : category },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}

// Separate featured masterclass (you could have logic for this, e.g. first one)
const featuredMasterclass = computed(() => {
    return props.courses.length > 0 ? props.courses[0] : null;
});

const regularMasterclasses = computed(() => {
    return props.courses.length > 1 ? props.courses.slice(1) : [];
});

const formatMonth = (dateString: string) => {
    if (!dateString) return 'Por anunciar';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-PE', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatTime = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit', hour12: false }) + ' (Perú)';
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses en Vivo', href: '#' },
];
</script>

<template>
    <Head title="Masterclasses - IEE" />
    
    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex min-h-screen flex-col font-sans', !isDashboard ? 'bg-[#FAFAF5]' : 'bg-transparent shadow-none']">
            <Navigation v-if="!isDashboard" />
    
            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-24 md:pt-32' : 'pt-0']">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Header -->
                <div class="mb-10 lg:mb-16 max-w-2xl">
                    <span class="inline-flex rounded-full bg-[#EADDFF] text-[#4F378B] px-3 py-1 text-[10px] font-bold uppercase tracking-widest mb-4">
                        Campus Digital Premium
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-[56px] font-serif font-bold text-[#1A1C19] leading-[1.1] mb-6 tracking-[-0.02em]">
                        Domina la Economía y Empresa con <span class="italic text-[#57572A]">Clases en Vivo</span>
                    </h1>
                    <p class="text-lg text-[#5F5E5E] leading-relaxed">
                        Accede a la excelencia académica desde cualquier lugar. Sesiones interactivas con expertos del sector financiero y estratégico.
                    </p>
                </div>

                <!-- Tabs -->
                <div class="flex flex-wrap gap-3 mb-12">
                    <button 
                        @click="applyFilters('Todas')"
                        class="px-5 py-2.5 rounded-full text-sm font-bold transition-colors shadow-sm"
                        :class="selectedCategory === 'Todas' ? 'bg-[#57572A] text-white' : 'bg-[#EAEAE0] text-[#57572A] hover:bg-[#dfdfd5]'"
                    >
                        Todos
                    </button>
                    <button 
                        v-for="cat in categories" 
                        :key="cat.id"
                        @click="applyFilters(cat.name)"
                        class="px-5 py-2.5 rounded-full text-sm font-bold transition-colors shadow-sm"
                        :class="selectedCategory === cat.name ? 'bg-[#57572A] text-white' : 'bg-[#EAEAE0] text-[#57572A] hover:bg-[#dfdfd5]'"
                    >
                        {{ cat.name }}
                    </button>
                </div>

                <!-- Featured Masterclass (if any) -->
                <!-- You can swap the order of regular/featured if you want it exactly like the photo (photo has regular top, featured bottom, but usually featured is top) -->
                <!-- The photo actually shows 3 regular cards on top, 1 huge text on bottom left + huge image on bottom right -->
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12" v-if="regularMasterclasses.length > 0">
                    <!-- Regular Cards -->
                    <article 
                        v-for="course in regularMasterclasses" 
                        :key="course.id"
                        class="bg-white rounded-2xl flex flex-col group overflow-hidden border border-[#C9C7B8]/20 shadow-[0_10px_20px_rgba(26,28,25,0.04)]"
                    >
                        <!-- Image Container -->
                        <div class="relative h-[200px] w-full bg-[#F4F4EF] overflow-hidden">
                            <img v-if="course.image" :src="course.image" :alt="course.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
                            <div class="absolute top-4 left-4 z-10">
                                <span class="px-3 py-1.5 rounded-full bg-[#D32F2F] text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-1.5 shadow-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    EN VIVO
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center gap-4 text-[13px] font-medium text-[#5F5E5E] mb-4">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#C9C7B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ course.start_date ? new Date(course.start_date).toLocaleDateString() : 'Por definir' }}
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#C9C7B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ course.start_time || 'Por definir' }}
                                </div>
                            </div>

                            <Link :href="route('cursos.show', course.slug)" class="mb-5 block">
                                <h3 class="font-serif font-bold text-xl text-[#1A1C19] leading-tight group-hover:text-[#57572A] transition-colors" :title="course.title">
                                    {{ course.title }}
                                </h3>
                            </Link>

                            <!-- Buttons -->
                            <div class="mt-auto flex flex-col gap-2">
                                <a :href="course.whatsapp_link || '#'" target="_blank" class="w-full justify-center flex items-center gap-2 rounded-xl text-[11px] uppercase tracking-widest font-bold px-4 py-3 bg-[#25D366] text-white hover:bg-[#20BE5A] transition-colors shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.115.55 4.183 1.597 6l-1.6 5.8 5.922-1.556c1.782.973 3.791 1.488 5.85 1.488 6.645 0 12.03-5.385 12.03-12.03S18.676 0 12.031 0zm0 21.905c-1.801 0-3.56-.484-5.111-1.405l-.367-.217-3.793.996 1.002-3.693-.238-.38c-.997-1.59-1.523-3.425-1.523-5.32 0-5.632 4.58-10.213 10.212-10.213 5.631 0 10.212 4.581 10.212 10.213 0 5.632-4.58 10.213-10.212 10.213zm5.602-7.662c-.307-.154-1.815-.898-2.097-1.002-.282-.104-.488-.154-.693.154-.206.308-.792 1.002-.971 1.206-.18.205-.36.23-.667.077-.308-.154-1.295-.478-2.467-1.525-.912-.815-1.528-1.821-1.708-2.129-.18-.308-.02-.475.134-.627.139-.138.307-.358.461-.537.154-.18.205-.308.308-.513.102-.205.051-.385-.026-.539-.077-.154-.693-1.673-.95-2.289-.25-.6-.505-.518-.694-.527-.18-.009-.385-.011-.59-.011-.205 0-.539.077-.821.385-.282.308-1.077 1.053-1.077 2.568 0 1.515 1.103 2.978 1.257 3.183.154.205 2.174 3.321 5.265 4.659.735.318 1.308.508 1.758.65.736.234 1.405.2 1.933.121.59-.089 1.815-.742 2.072-1.46.257-.719.257-1.335.18-1.461-.077-.126-.282-.203-.59-.357z"/></svg>
                                    UNIRSE AL GRUPO
                                </a>
                                <Link :href="route('cursos.show', course.slug)" class="w-full justify-center flex items-center gap-2 rounded-full text-[10px] uppercase tracking-widest font-bold px-4 py-2 border border-[#C9C7B8]/40 text-[#5F5E5E] hover:bg-[#F4F4EF] hover:text-[#1A1C19] transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">info</span>
                                    Ver Detalles
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Featured Masterclass Card -->
                <div v-if="featuredMasterclass" class="bg-[#F4F4EF] rounded-3xl overflow-hidden mt-8 mb-16 shadow-[0_20px_40px_rgba(26,28,25,0.06)] grid grid-cols-1 lg:grid-cols-2 relative">
                    <!-- Left Content -->
                    <div class="p-8 md:p-12 lg:p-14 flex flex-col justify-center">
                        <span class="inline-flex w-max rounded-full bg-[#EADDFF] text-[#4F378B] px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest mb-6">
                            Masterclass Destacada
                        </span>
                        <h2 class="font-serif italic font-bold text-4xl lg:text-5xl text-[#1A1C19] leading-tight mb-6">
                            {{ featuredMasterclass.title }}
                        </h2>
                        <p class="text-[#5F5E5E] leading-relaxed mb-8 max-w-lg">
                            {{ featuredMasterclass.description || 'Descubre los algoritmos y la economía conductual que están redefiniendo las finanzas.' }}
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-6 mb-10">
                            <div class="flex items-center gap-2 text-sm font-bold text-[#1A1C19]">
                                <svg class="w-5 h-5 text-[#57572A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ formatMonth(featuredMasterclass.lessons?.[0]?.start_time) }}
                            </div>
                            <div class="hidden sm:block w-[1px] h-6 bg-[#C9C7B8]/50"></div>
                            <div v-if="featuredMasterclass.lessons?.[0]?.start_time" class="flex items-center gap-2 text-sm font-bold text-[#1A1C19]">
                                <svg class="w-5 h-5 text-[#57572A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ formatTime(featuredMasterclass.lessons?.[0]?.start_time) }}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 mt-2">
                            <a :href="featuredMasterclass.lessons?.[0]?.live_link || '#'" target="_blank" class="w-full sm:w-auto justify-center inline-flex items-center gap-2 rounded-xl text-[13px] uppercase tracking-widest font-bold px-8 py-4 bg-[#57572A] text-white hover:bg-[#4a4a24] transition-all shadow-xl hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-5a2 2 0 00-2-2h-3.586a1 1 0 01-.707-.293l-1.414-1.414A1 1 0 0013.586 11H12v6a2 2 0 002 2h3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11h-2a2 2 0 00-2 2v6h2"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                UNIRSE AL GRUPO
                            </a>
                            <Link :href="route('cursos.show', featuredMasterclass.slug)" class="w-full sm:w-auto justify-center inline-flex items-center gap-2 rounded-xl text-[13px] uppercase tracking-widest font-bold px-8 py-4 border-2 border-[#57572A]/20 text-[#57572A] hover:bg-[#57572A]/5 transition-all">
                                MÁS INFORMACIÓN
                            </Link>
                        </div>
                    </div>

                    <!-- Right Image Area -->
                    <div class="relative min-h-[300px] lg:min-h-[500px]">
                        <img v-if="featuredMasterclass.image" :src="featuredMasterclass.image" :alt="featuredMasterclass.title" class="absolute inset-0 w-full h-full object-cover rounded-r-3xl" />
                        <div v-else class="absolute inset-0 w-full h-full bg-gradient-to-tr from-[#57572A]/80 to-[#1A1C19]/90 rounded-r-3xl flex items-center justify-center">
                            <span class="text-white/20 text-4xl font-serif">IEE</span>
                        </div>
                        
                        <!-- Mini EN VIVO pill on top right -->
                        <div class="absolute top-6 right-6">
                            <span class="px-3 py-1.5 rounded-full bg-[#D32F2F] text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-1.5 shadow-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                EN VIVO
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="props.courses.length === 0" class="py-20 text-center bg-white rounded-2xl shadow-sm border border-[#C9C7B8]/20">
                    <p class="text-[#5F5E5E] font-serif text-lg">No hay masterclasses disponibles en esta categoría.</p>
                </div>

            </div>
        </main>
        </div>
    </component>
</template>
