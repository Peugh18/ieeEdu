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

// We process the static adminNavItems to inject the dynamic badge
const adminGroups = computed<NavGroup[]>(() => {
    return adminNavItems.map(group => ({
        ...group,
        items: group.items.map(item => {
            if (item.title === 'Pagos' && pendingPaymentsCount.value > 0) {
                return { ...item, badge: pendingPaymentsCount.value > 9 ? '9+' : pendingPaymentsCount.value };
            }
            return item;
        })
    }));
});

function isActive(href: string): boolean {
    try {
        const resolved = route(href, undefined, false);
        return page.url.startsWith(resolved);
    } catch {
        return false;
    }
}
</script>

<template>
    <div class="px-3 py-2 overflow-y-auto custom-scrollbar flex-1">
        
        <!-- Vista Administrador -->
        <template v-if="isAdmin">
            <template v-for="(group, gIdx) in adminGroups" :key="gIdx">
                <!-- Section Label -->
                <div class="px-3 pt-5 pb-2" v-if="state === 'expanded'">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant/40">
                        {{ group.label }}
                    </span>
                </div>
                <div v-else class="h-5"></div>

                <!-- Section Items -->
                <nav class="flex flex-col gap-1">
                    <Tooltip v-for="item in group.items" :key="item.title" :disabled="state === 'expanded'">
                        <TooltipTrigger as-child>
                            <Link 
                                :href="route(item.href)" 
                                class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300"
                                :class="isActive(item.href) 
                                    ? 'bg-primary/10 dark:bg-white/5 border-l-[3px] border-l-primary text-primary dark:!text-primary font-bold' 
                                    : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-medium border-l-[3px] border-l-transparent'"
                            >
                                <component 
                                    :is="item.icon" 
                                    class="w-[18px] h-[18px] flex-shrink-0" 
                                />
                                
                                <span class="text-sm tracking-tight truncate flex-1" v-if="state === 'expanded'">
                                    {{ item.title }}
                                </span>
                                
                                <span v-if="item.badge && state === 'expanded'" 
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="isActive(item.href) ? 'bg-primary text-white dark:!text-black' : 'bg-surface-container-highest text-on-surface-variant'"
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

                <!-- Sutil separator between sections, except the last one -->
                <div v-if="gIdx < adminGroups.length - 1" class="border-t border-outline-variant/10 my-2 mx-2"></div>
            </template>
        </template>

        <!-- Vista Estudiante (Plana) -->
        <template v-else>
            <nav class="flex flex-col gap-1 pt-2">
                <Tooltip v-for="item in studentNavItems" :key="item.title" :disabled="state === 'expanded'">
                    <TooltipTrigger as-child>
                        <Link 
                            :href="route(item.href)" 
                            class="relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300"
                            :class="isActive(item.href) 
                                ? 'bg-primary/10 dark:bg-white/5 border-l-[3px] border-l-primary text-primary dark:!text-primary font-bold' 
                                : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-medium border-l-[3px] border-l-transparent'"
                        >
                            <component 
                                :is="item.icon" 
                                class="w-[18px] h-[18px] flex-shrink-0" 
                            />
                            
                            <span class="text-sm tracking-tight truncate flex-1" v-if="state === 'expanded'">
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
