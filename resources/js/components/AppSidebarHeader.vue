<script setup lang="ts">
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen } from 'lucide-vue-next';

const props = withDefaults(defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>(), {
    breadcrumbs: () => []
});

const page = usePage();
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-4 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12 md:px-4 bg-white/80 backdrop-blur-md sticky top-0 z-50"
    >
        <div class="flex items-center gap-4">
            <!-- Branding / Logo (Icon Only as requested) -->
            <Link :href="route('dashboard')" class="flex items-center gap-2 group mr-2">
                <div class="p-1.5 bg-primary/10 rounded-lg group-hover:bg-primary/20 transition-colors">
                    <BookOpen class="w-4 h-4 text-primary" />
                </div>
            </Link>

            <SidebarTrigger class="-ml-1 text-on-surface-variant hover:text-primary transition-colors" />
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
                    { title: 'Consultoría', href: '#' },
                    { title: 'Publicaciones', href: route('student.explore.publications') },
                    { title: 'Masterclass', href: '#' }
                ]" 
                :key="link.title"
                :href="link.href"
                class="text-[11px] font-bold text-on-surface-variant hover:text-primary uppercase tracking-[0.2em] transition-all relative group"
            >
                {{ link.title }}
                <span class="absolute -bottom-1 left-0 w-0 h-[2px] bg-primary transition-all group-hover:w-full"></span>
            </Link>
        </nav>

        <!-- Profile / Right Section -->
        <div class="flex items-center gap-6">
            <div class="hidden lg:block h-4 w-[1px] bg-outline-variant/30"></div>
            <Link 
                :href="route('profile.edit')"
                class="flex items-center gap-3 group"
            >
                <span class="text-[10px] font-bold text-on-surface-variant/60 group-hover:text-primary uppercase tracking-widest transition-colors cursor-pointer hidden sm:inline">Perfil</span>
                <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/10 flex items-center justify-center text-[10px] font-bold text-primary italic uppercase group-hover:scale-110 transition-transform">
                    {{ (page.props.auth as any).user.name.charAt(0) }}
                </div>
            </Link>
        </div>
    </header>
</template>
