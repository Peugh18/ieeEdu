<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { SidebarTrigger } from '@/components/ui/sidebar';
import BrandLogo from '@/components/BrandLogo.vue';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Globe } from 'lucide-vue-next';

const props = withDefaults(defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>(), {
    breadcrumbs: () => []
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-4 border-b border-sidebar-border/70 px-4 md:px-6 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 bg-white/80 dark:bg-on-background/90 backdrop-blur-md sticky top-0 z-50 dark:border-[rgba(231,230,171,0.08)]"
    >
        <div class="flex items-center gap-4">
            <!-- IEE Logo → Landing page -->
            <Link :href="route('home')" class="flex items-center group mr-1 shrink-0">
                <BrandLogo imageClass="h-7 w-auto object-contain opacity-90 group-hover:opacity-100 transition-opacity" />
            </Link>

            <SidebarTrigger class="-ml-1 flex text-on-surface-variant hover:text-primary transition-colors" />
            <div v-if="props.breadcrumbs.length > 0" class="hidden lg:block h-4 w-[1px] bg-outline-variant/30 ml-2"></div>
            <template v-if="props.breadcrumbs.length > 0">
                <Breadcrumb class="hidden sm:block">
                    <BreadcrumbList>
                        <template v-for="(item, index) in props.breadcrumbs" :key="index">
                            <BreadcrumbItem>
                                <template v-if="index === props.breadcrumbs.length - 1">
                                    <BreadcrumbPage class="text-[10px] font-bold text-on-surface uppercase tracking-widest italic">{{ item.title }}</BreadcrumbPage>
                                </template>
                                <template v-else>
                                    <BreadcrumbLink :href="item.href" class="text-[10px] font-bold text-on-surface-variant/40 hover:text-primary uppercase tracking-widest transition-colors">
                                        {{ item.title }}
                                    </BreadcrumbLink>
                                </template>
                            </BreadcrumbItem>
                            <BreadcrumbSeparator v-if="index !== props.breadcrumbs.length - 1" />
                        </template>
                    </BreadcrumbList>
                </Breadcrumb>
            </template>
        </div>

        <!-- Dynamic Navigation Links & Website Redirect -->
        <div class="flex items-center gap-6">
            <nav class="hidden md:flex items-center gap-8" v-if="user?.role !== 'admin'">
                <Link 
                    v-for="link in [
                        { title: 'Cursos', href: route('student.explore.courses') },
                        { title: 'Consultoría', href: route('student.explore.consultoria') },
                        { title: 'Publicaciones', href: route('student.explore.publications') },
                        { title: 'Masterclass', href: route('student.explore.masterclasses') }
                    ]" 
                    :key="link.title"
                    :href="link.href"
                    class="text-[11px] font-bold text-on-surface-variant hover:text-primary uppercase tracking-[0.2em] transition-all relative group"
                >
                    {{ link.title }}
                    <span class="absolute -bottom-1 left-0 w-0 h-[2px] bg-primary transition-all group-hover:w-full"></span>
                </Link>
            </nav>

            <Link 
                :href="route('home')" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200/60 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 hover:text-primary hover:border-primary/30 dark:text-slate-400 dark:hover:text-primary transition-all duration-300"
                title="Volver al Sitio Web Principal"
            >
                <Globe class="w-3.5 h-3.5 text-primary/70 animate-pulse" />
                <span class="hidden sm:inline">Regresar a la web</span>
            </Link>
        </div>

    </header>
</template>
