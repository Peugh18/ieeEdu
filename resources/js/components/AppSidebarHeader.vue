<script setup lang="ts">
import BrandLogo from '@/components/BrandLogo.vue';
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Globe } from 'lucide-vue-next';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <header
        class="border-sidebar-border/70 sticky top-0 z-50 flex h-16 shrink-0 items-center justify-between gap-4 border-b bg-white/80 px-4 backdrop-blur-md transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 dark:border-[rgba(231,230,171,0.08)] dark:bg-on-background/90 md:px-6"
    >
        <div class="flex items-center gap-4">
            <!-- IEE Logo → Landing page -->
            <Link :href="route('home')" class="group mr-1 flex shrink-0 items-center">
                <BrandLogo imageClass="h-7 w-auto object-contain opacity-90 group-hover:opacity-100 transition-opacity" />
            </Link>

            <SidebarTrigger class="-ml-1 flex text-on-surface-variant transition-colors hover:text-primary" />
            <div v-if="props.breadcrumbs.length > 0" class="ml-2 hidden h-4 w-[1px] bg-outline-variant/30 lg:block"></div>
            <template v-if="props.breadcrumbs.length > 0">
                <Breadcrumb class="hidden sm:block">
                    <BreadcrumbList>
                        <template v-for="(item, index) in props.breadcrumbs" :key="index">
                            <BreadcrumbItem>
                                <template v-if="index === props.breadcrumbs.length - 1">
                                    <BreadcrumbPage class="text-[10px] font-bold uppercase italic tracking-widest text-on-surface">{{
                                        item.title
                                    }}</BreadcrumbPage>
                                </template>
                                <template v-else>
                                    <BreadcrumbLink
                                        :href="item.href"
                                        class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/40 transition-colors hover:text-primary"
                                    >
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
            <nav class="hidden items-center gap-8 md:flex" v-if="user?.role !== 'admin'">
                <Link
                    v-for="link in [
                        { title: 'Cursos', href: route('student.explore.courses') },
                        { title: 'Consultoría', href: route('student.explore.consultoria') },
                        { title: 'Publicaciones', href: route('student.explore.publications') },
                        { title: 'Masterclass', href: route('student.explore.masterclasses') },
                    ]"
                    :key="link.title"
                    :href="link.href"
                    class="group relative text-[11px] font-bold uppercase tracking-[0.2em] text-on-surface-variant transition-all hover:text-primary"
                >
                    {{ link.title }}
                    <span class="absolute -bottom-1 left-0 h-[2px] w-0 bg-primary transition-all group-hover:w-full"></span>
                </Link>
            </nav>

            <Link
                :href="route('home')"
                class="flex items-center gap-1.5 rounded-xl border border-slate-200/60 bg-slate-50/50 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.15em] text-slate-500 transition-all duration-300 hover:border-primary/30 hover:text-primary dark:border-slate-800/80 dark:bg-slate-900/50 dark:text-slate-400 dark:hover:text-primary"
                title="Volver al Sitio Web Principal"
            >
                <Globe class="h-3.5 w-3.5 animate-pulse text-primary/70" />
                <span class="hidden sm:inline">Regresar a la web</span>
            </Link>
        </div>
    </header>
</template>
