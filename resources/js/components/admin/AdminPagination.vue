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
    <div v-if="links.length > 3" class="mt-20 flex flex-col items-center gap-6 pb-20 border-t border-on-background/5 pt-12 dark:border-primary-fixed/10">
        <span class="text-[8px] font-black uppercase tracking-[0.4em] text-[#9ca3af] dark:text-[#6b7280]">
            {{ label ?? 'Navegación de Archivo' }}
        </span>
        <div class="flex items-center gap-2">
            <template v-for="link in links" :key="link.label">
                <span
                    v-if="!link.url"
                    v-html="link.label"
                    class="px-5 py-2 text-[9px] font-black uppercase tracking-widest text-on-background/20 pointer-events-none dark:text-primary-fixed/20"
                ></span>
                <a
                    v-else
                    :href="link.url"
                    v-html="link.label"
                    class="min-w-[40px] h-[40px] flex items-center justify-center rounded-full text-[9px] font-black uppercase tracking-widest transition-all"
                    :class="[
                        link.active
                        ? 'bg-on-background text-primary-fixed shadow-xl scale-110 dark:bg-primary-fixed dark:text-on-background'
                        : 'bg-white text-on-background hover:bg-on-background/5 ring-1 ring-on-background/5 dark:bg-[#2a2a1a] dark:text-primary-fixed dark:ring-primary-fixed/10'
                    ]"
                ></a>
            </template>
        </div>
    </div>
</template>
