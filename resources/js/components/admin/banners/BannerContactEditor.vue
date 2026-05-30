<script setup lang="ts">
import { Link as LinkIcon } from 'lucide-vue-next';

const slide = defineModel<{
    whatsapp_number: string;
    contact_email: string;
    contact_address: string;
}>('slide', { required: true });
</script>

<template>
    <div class="rounded-[2.5rem] p-8 shadow-sm border border-outline-variant/20 bg-surface-container-lowest space-y-6">
        <h3 class="text-xl font-serif flex items-center gap-2 text-on-surface">
            <LinkIcon class="w-5 h-5 text-primary" />
            Datos de Contacto del Hero
            <span class="ml-2 text-xs font-bold uppercase tracking-widest px-2 py-0.5 rounded-lg bg-surface-container-high text-primary">Solo Consultoría</span>
        </h3>
        <p class="text-xs font-medium text-on-surface-variant">
            Estos datos aparecerán como accesos rápidos en el hero de la página <strong>/consultoria</strong>. Puedes dejar en blanco los que no desees mostrar.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2 text-on-surface-variant">Número WhatsApp (solo dígitos con código de país)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-on-surface-variant">+</span>
                    <input v-model="slide.whatsapp_number" type="text" class="w-full h-14 rounded-2xl pl-8 pr-5 text-sm font-bold outline-none transition-all border border-outline-variant/30 bg-surface-container-high text-on-surface focus:ring-primary focus:border-primary"
                        placeholder="51999000000" />
                </div>
                <p class="text-[10px] pl-2 text-on-surface-variant/60">Ej: 51961867761 (sin espacios ni guiones)</p>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2 text-on-surface-variant">Correo electrónico de contacto</label>
                <input v-model="slide.contact_email" type="email" class="w-full h-14 rounded-2xl px-5 text-sm font-bold outline-none transition-all border border-outline-variant/30 bg-surface-container-high text-on-surface focus:ring-primary focus:border-primary"
                    placeholder="info@iee.edu.pe" />
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="text-[10px] font-extrabold uppercase tracking-widest ml-2 text-on-surface-variant">Dirección / Ubicación</label>
                <input v-model="slide.contact_address" type="text" class="w-full h-14 rounded-2xl px-5 text-sm font-bold outline-none transition-all border border-outline-variant/30 bg-surface-container-high text-on-surface focus:ring-primary focus:border-primary"
                    placeholder="Trujillo, La Libertad — Perú" />
            </div>
        </div>
    </div>
</template>
