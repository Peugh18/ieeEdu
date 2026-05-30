<script setup lang="ts">
import { Download, FileImage } from 'lucide-vue-next';
import { computed } from 'vue';
import { storageUrl } from '@/lib/storageUrl';

const props = defineProps<{ comprobante: string | null }>();

const comprobanteUrl = computed(() => {
    if (!props.comprobante) return null;
    if (props.comprobante.startsWith('http') || props.comprobante.startsWith('/')) {
        return props.comprobante;
    }
    return storageUrl(props.comprobante) ?? null;
});
</script>

<template>
    <div class="rounded-3xl border border-outline-variant/10 bg-surface-container-lowest p-6 shadow-sm">
        <p class="mb-4 text-xs font-bold uppercase tracking-widest text-on-surface-variant">Comprobante</p>

        <template v-if="comprobanteUrl">
            <a :href="comprobanteUrl" target="_blank" rel="noopener">
                <img
                    :src="comprobanteUrl"
                    alt="Comprobante de pago"
                    class="w-full rounded-2xl border border-outline-variant/10 object-contain max-h-64 hover:opacity-90 transition-opacity cursor-zoom-in"
                />
            </a>
            <a
                :href="comprobanteUrl"
                download
                class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-primary hover:underline"
            >
                <Download class="h-3.5 w-3.5" />
                Descargar imagen
            </a>
        </template>
        <div v-else class="flex flex-col items-center gap-2 rounded-2xl bg-surface-container-low py-8 text-center text-on-surface-variant">
            <FileImage class="h-8 w-8 text-on-surface-variant/30" />
            <p class="text-sm">Sin comprobante adjunto</p>
        </div>
    </div>
</template>
