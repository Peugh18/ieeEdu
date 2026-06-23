<script setup lang="ts">
import { useSidebar } from '@/components/ui/sidebar';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { adminNavItems } from '@/config/admin-nav';
import { studentNavItems } from '@/config/student-nav';
import type { SharedData, User } from '@/types';
import type { NavGroup, NavItem } from '@/types/navigation';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const isAdmin = user.role === 'admin';
const { state } = useSidebar();

const pendingPaymentsCount = computed(() => page.props.admin_nav?.pending_payments || 0);

const adminGroups = computed<NavGroup[]>(() => {
    return adminNavItems.map((group) => ({
        ...group,
        items: group.items.map((item) => {
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
    const paths = item.matchPaths?.length ? item.matchPaths : [routePath(item.href)].filter(Boolean);

    return paths.some((path) => current === path || current.startsWith(`${path}/`));
}
</script>

<template>
    <div class="custom-scrollbar flex-1 overflow-y-auto px-2 py-1">
        <template v-if="isAdmin">
            <template v-for="(group, gIdx) in adminGroups" :key="group.label ?? gIdx">
                <div v-if="gIdx > 0" class="mx-3 border-t border-outline-variant/20" :class="state === 'expanded' ? 'mb-1 mt-3' : 'my-2'" />

                <div v-if="group.label && state === 'expanded'" class="flex items-center gap-2 px-3 pb-1.5 pt-1">
                    <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.12em] text-on-surface-variant/70">
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
                                class="relative flex items-center gap-3 rounded-xl px-3 py-2 transition-all duration-300"
                                :class="
                                    isActive(item)
                                        ? 'dark:bg-[#e7e6ab]/12 border-l-[3px] border-l-primary bg-primary/10 font-bold text-primary dark:!text-primary'
                                        : 'border-l-[3px] border-l-transparent font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface dark:hover:bg-white/10 dark:hover:text-white'
                                "
                            >
                                <component :is="item.icon" class="h-[18px] w-[18px] flex-shrink-0" />

                                <span v-if="state === 'expanded'" class="flex-1 truncate text-sm tracking-tight">
                                    {{ item.title }}
                                </span>

                                <span
                                    v-if="item.badge && state === 'expanded'"
                                    class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                    :class="
                                        isActive(item)
                                            ? 'bg-primary text-white dark:!text-black'
                                            : 'bg-surface-container-highest text-on-surface-variant'
                                    "
                                >
                                    {{ item.badge }}
                                </span>
                            </Link>
                        </TooltipTrigger>
                        <TooltipContent side="right" class="border-outline-variant/10 bg-surface-container-highest font-bold text-on-surface">
                            <div class="flex items-center gap-2">
                                <span>{{ item.title }}</span>
                                <span v-if="item.badge" class="rounded bg-primary/20 px-1.5 py-0.5 text-[10px] text-primary">{{ item.badge }}</span>
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
                            class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all duration-300"
                            :class="
                                isActive(item)
                                    ? 'dark:bg-[#e7e6ab]/12 border-l-[3px] border-l-primary bg-primary/10 font-bold text-primary dark:!text-primary'
                                    : 'border-l-[3px] border-l-transparent font-medium text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface dark:hover:bg-white/10 dark:hover:text-white'
                            "
                        >
                            <component :is="item.icon" class="h-[18px] w-[18px] flex-shrink-0" />

                            <span v-if="state === 'expanded'" class="flex-1 truncate text-sm tracking-tight">
                                {{ item.title }}
                            </span>
                        </Link>
                    </TooltipTrigger>
                    <TooltipContent side="right" class="border-outline-variant/10 bg-surface-container-highest font-bold text-on-surface">
                        <p>{{ item.title }}</p>
                    </TooltipContent>
                </Tooltip>
            </nav>
        </template>
    </div>
</template>
