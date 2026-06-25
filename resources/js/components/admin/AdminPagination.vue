<script setup lang="ts">
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

defineProps<{
    links: PaginationLink[];
    label?: string;
}>();
</script>

<template>
    <div
        v-if="links.length > 3"
        class="mt-20 flex flex-col items-center gap-6 border-t border-on-background/5 pb-20 pt-12 dark:border-primary-fixed/10"
    >
        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#9ca3af] dark:text-[#6b7280]">
            {{ label ?? 'Navegación de Archivo' }}
        </span>
        <div class="flex items-center gap-2">
            <template v-for="link in links" :key="link.label">
                <span
                    v-if="!link.url"
                    v-html="link.label"
                    class="pointer-events-none px-5 py-2 text-xs font-black uppercase tracking-widest text-on-background/20 dark:text-primary-fixed/20"
                ></span>
                <a
                    v-else
                    :href="link.url"
                    v-html="link.label"
                    class="flex h-[40px] min-w-[40px] items-center justify-center rounded-full text-xs font-black uppercase tracking-widest transition-all"
                    :class="[
                        link.active
                            ? 'scale-110 bg-on-background text-primary-fixed shadow-xl dark:bg-primary-fixed dark:text-on-background'
                            : 'bg-white text-on-background ring-1 ring-on-background/5 hover:bg-on-background/5 dark:bg-[#2a2a1a] dark:text-primary-fixed dark:ring-primary-fixed/10',
                    ]"
                ></a>
            </template>
        </div>
    </div>
</template>
