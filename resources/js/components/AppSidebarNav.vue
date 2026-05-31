<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { useSidebar } from '@/components/ui/sidebar';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import type { SharedData, User } from '@/types';
import { adminNavItems } from '@/config/admin-nav';
import { studentNavItems } from '@/config/student-nav';
import type { NavGroup, NavItem } from '@/types/navigation';
import { computed } from 'vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const isAdmin = user.role === 'admin';
const { state } = useSidebar();

const pendingPaymentsCount = computed(() => page.props.admin_nav?.pending_payments || 0);

const adminGroups = computed<NavGroup[]>(() => {
    return adminNavItems.map(group => ({
        ...group,
        items: group.items.map(item => {
            if (item.href === 'admin.payments.index' && pendingPaymentsCount.value > 0) {
                return {
                    ...item,
                    badge: pendingPaymentsCount.value > 9 ? '9+' : pendingPaymentsCount.value,
                };
            }
            return item;
        }),
    }));
});

function routePath(routeName: string): string {
    try {
        return route(routeName, undefined, false).split('?')[0];
    } catch {
        return '';
    }
}

function isActive(item: NavItem): boolean {
    const current = page.url.split('?')[0];
    const paths = item.matchPaths?.length
        ? item.matchPaths
        : [routePath(item.href)].filter(Boolean);

    return paths.some((path) => current === path || current.startsWith(`${path}/`));
}
</script>

<template>
    <div class="px-2 py-1 overflow-y-auto custom-scrollbar flex-1">

        <template v-if="isAdmin">
            <template v-for="(group, gIdx) in adminGroups" :key="group.label ?? gIdx">
                <div
                    v-if="gIdx > 0"
                    class="mx-3 border-t border-outline-variant/20"
                    :class="state === 'expanded' ? 'mt-3 mb-1' : 'my-2'"
                />

                <div
                    v-if="group.label && state === 'expanded'"
                    class="px-3 pt-1 pb-1.5 flex items-center gap-2"
                >
                    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-on-surface-variant/70 whitespace-nowrap">
                        {{ group.label }}
                    </span>
                    <span class="h-px flex-1 bg-outline-variant/25" />
                </div>
                <div v-else-if="group.label && state === 'collapsed'" class="h-1" />

                <nav class="flex flex-col gap-0.5" :class="gIdx === 0 ? 'pt-0.5' : ''">
                    <Tooltip v-for="item in group.items" :key="item.href" :disabled="state === 'expanded'">
                        <TooltipTrigger as-child>
                            <Link
                                :href="route(item.href)"
                                class="relative flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-300"
                                :class="isActive(item)
                                    ? 'bg-primary/10 dark:bg-[#e7e6ab]/12 border-l-[3px] border-l-primary text-primary dark:!text-primary font-bold'
                                    : 'text-on-surface-variant hover:bg-surface-container-high dark:hover:bg-white/10 hover:text-on-surface dark:hover:text-white font-medium border-l-[3px] border-l-transparent'"
                            >
                                <component
                                    :is="item.icon"
                                    class="w-[18px] h-[18px] flex-shrink-0"
                                />

                                <span v-if="state === 'expanded'" class="text-sm tracking-tight truncate flex-1">
                                    {{ item.title }}
                                </span>

                                <span
                                    v-if="item.badge && state === 'expanded'"
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="isActive(item) ? 'bg-primary text-white dark:!text-black' : 'bg-surface-container-highest text-on-surface-variant'"
                                >
                                    {{ item.badge }}
                                </span>
                            </Link>
                        </TooltipTrigger>
                        <TooltipContent side="right" class="bg-surface-container-highest border-outline-variant/10 text-on-surface font-bold">
                            <div class="flex items-center gap-2">
                                <span>{{ item.title }}</span>
                                <span v-if="item.badge" class="bg-primary/20 text-primary px-1.5 py-0.5 rounded text-[10px]">{{ item.badge }}</span>
                            </div>
                        </TooltipContent>
                    </Tooltip>
                </nav>
            </template>
        </template>

        <template v-else>
            <nav class="flex flex-col gap-1 pt-2">
                <Tooltip v-for="item in studentNavItems" :key="item.title" :disabled="state === 'expanded'">
                    <TooltipTrigger as-child>
                        <Link
                            :href="route(item.href)"
                            class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300"
                            :class="isActive(item)
                                ? 'bg-primary/10 dark:bg-[#e7e6ab]/12 border-l-[3px] border-l-primary text-primary dark:!text-primary font-bold'
                                : 'text-on-surface-variant hover:bg-surface-container-high dark:hover:bg-white/10 hover:text-on-surface dark:hover:text-white font-medium border-l-[3px] border-l-transparent'"
                        >
                            <component
                                :is="item.icon"
                                class="w-[18px] h-[18px] flex-shrink-0"
                            />

                            <span v-if="state === 'expanded'" class="text-sm tracking-tight truncate flex-1">
                                {{ item.title }}
                            </span>
                        </Link>
                    </TooltipTrigger>
                    <TooltipContent side="right" class="bg-surface-container-highest border-outline-variant/10 text-on-surface font-bold">
                        <p>{{ item.title }}</p>
                    </TooltipContent>
                </Tooltip>
            </nav>
        </template>
    </div>
</template>
