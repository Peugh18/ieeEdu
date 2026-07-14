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
        <div class="flex w-full items-center gap-4">
            <!-- IEE Logo → Solo visible en móviles cuando la barra lateral principal se oculta -->
            <Link :href="route('home')" class="group mr-1 flex shrink-0 items-center md:hidden">
                <BrandLogo imageClass="h-7 w-auto object-contain opacity-90 transition-opacity group-hover:opacity-100" />
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
    </header>
</template>
