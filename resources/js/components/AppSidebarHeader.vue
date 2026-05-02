<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const props = withDefaults(defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>(), {
    breadcrumbs: () => []
});

const page = usePage();
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-4 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4 bg-white/80 dark:bg-on-background/90 backdrop-blur-md sticky top-0 z-50 dark:border-[rgba(231,230,171,0.08)]"
    >
        <div class="flex items-center gap-4">
            <!-- IEE Logo → Landing page -->
            <Link :href="route('home')" class="flex items-center group mr-1 shrink-0">
                <img
                    src="/images/empresa/LogoLight.png"
                    alt="Instituto de Economía y Empresa"
                    class="h-7 w-auto object-contain opacity-90 group-hover:opacity-100 transition-opacity"
                />
            </Link>

            <SidebarTrigger class="-ml-1 hidden lg:flex text-on-surface-variant hover:text-primary transition-colors" />
            <div class="hidden lg:block h-4 w-[1px] bg-outline-variant/30 ml-2"></div>
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

        <!-- Dynamic Navigation Links (from Landing) -->
        <nav class="hidden md:flex items-center gap-10">
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

    </header>
</template>
