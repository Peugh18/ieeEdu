<script setup lang="ts">
/**
 * AdminIconButton — botón circular con ícono (edit, delete, link, custom)
 *
 * variant:
 *   'default'  → bg-surface-container dark, hover dark/cream    (grid actions small)
 *   'outline'  → bg-white border, hover dark/cream       (list actions large)
 *   'danger'   → texto rojo, hover bg-red                (delete)
 *   'ghost'    → text-slate-400 hover color configurable (Courses table)
 */
withDefaults(defineProps<{
    as?: 'button' | 'a';
    href?: string;
    target?: string;
    variant?: 'default' | 'outline' | 'danger' | 'ghost';
    size?: 'sm' | 'md'; // sm=w-8 h-8, md=w-10 h-10
    hoverColor?: string; // para ghost: 'blue' | 'rose' | 'emerald' | 'amber' | 'brand'
    disabled?: boolean;
    title?: string;
}>(), {
    as: 'button',
    variant: 'default',
    size: 'sm',
    hoverColor: 'brand',
    disabled: false,
});

const emit = defineEmits<{ click: [] }>();

const sizeMap = {
    sm: 'w-8 h-8',
    md: 'w-10 h-10',
};

const iconSizeMap = {
    sm: 'h-3 w-3',
    md: 'h-4 w-4',
};

const variantMap = {
    default: 'bg-surface-container text-on-background hover:bg-on-background hover:text-primary-fixed dark:bg-[#2a2a1a] dark:text-primary-fixed dark:hover:bg-primary-fixed dark:hover:text-on-background',
    outline: 'bg-white border border-on-background/5 text-[#9ca3af] hover:bg-on-background hover:text-primary-fixed shadow-sm hover:shadow-md dark:bg-[#2a2a1a] dark:border-primary-fixed/10 dark:text-primary-fixed/40 dark:hover:bg-primary-fixed dark:hover:text-on-background',
    danger: 'bg-surface-container text-red-400 hover:bg-red-500 hover:text-white dark:bg-[#2a2a1a] dark:text-red-400',
    ghost: '',
};

const ghostHoverMap: Record<string, string> = {
    blue: 'hover:bg-blue-50 hover:text-blue-600',
    rose: 'hover:bg-rose-50 hover:text-rose-600',
    emerald: 'hover:bg-emerald-50 hover:text-emerald-600',
    amber: 'hover:bg-amber-50 hover:text-amber-600',
    brand: 'hover:bg-primary/5 hover:text-primary',
};
</script>

<template>
    <component
        :is="as"
        :href="as === 'a' ? href : undefined"
        :target="as === 'a' ? target : undefined"
        :disabled="as === 'button' ? disabled : undefined"
        :title="title"
        @click="as === 'button' ? emit('click') : undefined"
        class="rounded-full flex items-center justify-center transition-all duration-300 shadow-sm flex-shrink-0"
        :class="[
            sizeMap[size],
            variant !== 'ghost' ? variantMap[variant] : `p-2 rounded-xl text-slate-400 transition-all hover:scale-110 ${ghostHoverMap[hoverColor]}`,
            disabled ? 'opacity-20 pointer-events-none' : ''
        ]"
    >
        <slot :iconClass="iconSizeMap[size]" />
    </component>
</template>
