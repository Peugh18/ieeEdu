<script setup lang="ts">
/**
 * AdminStatsCard — tarjeta de estadística reutilizable
 *
 * Detectada 7 veces: Users.vue (×4) + Categories.vue (×3)
 * Patrón: rounded-[2rem] bg-white p-6/7 border shadow-sm group overflow-hidden
 *         con ícono decorativo absoluto y métricas.
 *
 * variant:
 *   'default' → bg-white
 *   'accent'  → bg-primary con texto claro (tarjeta destacada)
 */
import { Component } from 'vue';

withDefaults(defineProps<{
    label: string;
    sublabel?: string;
    value: string | number;
    valueClass?: string;
    badge?: string;
    badgeClass?: string;
    iconClass?: string;
    variant?: 'default' | 'accent';
}>(), {
    sublabel: '',
    valueClass: 'text-slate-900',
    badge: '',
    badgeClass: 'bg-slate-100 text-slate-500',
    iconClass: 'text-slate-400/10',
    variant: 'default',
});
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-[2rem] p-6 border shadow-sm transition-all duration-300"
        :class="variant === 'accent'
            ? 'bg-primary border-transparent shadow-xl shadow-primary/10 hover:scale-[1.01]'
            : 'bg-white border-slate-100 hover:shadow-md hover:border-slate-200'"
    >
        <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
            <div class="flex items-center justify-between">
                <span
                    class="text-[10px] font-extrabold uppercase tracking-widest"
                    :class="variant === 'accent' ? 'text-outline-variant' : 'text-slate-400'"
                >
                    {{ label }}
                </span>
                <slot name="icon" />
            </div>
            <div>
                <p
                    class="text-[10px] font-bold uppercase tracking-widest leading-none"
                    :class="variant === 'accent' ? 'text-outline-variant' : 'text-slate-400'"
                >
                    {{ sublabel }}
                </p>
                <p
                    class="text-4xl font-black mt-1"
                    :class="variant === 'accent' ? 'text-white' : valueClass"
                >
                    {{ value }}
                </p>
            </div>
        </div>

        <!-- Decorative background icon -->
        <div
            class="absolute -bottom-4 -right-4 w-20 h-20 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity"
            :class="variant === 'accent' ? 'text-white' : iconClass"
        >
            <slot name="bg-icon" />
        </div>
    </div>
</template>
