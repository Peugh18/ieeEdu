<script setup lang="ts">
import { Link as LinkIcon } from 'lucide-vue-next';

const slide = defineModel<{
    whatsapp_number: string;
    contact_email: string;
    contact_address: string;
}>('slide', { required: true });
</script>

<template>
    <div class="space-y-6 rounded-[2.5rem] border border-outline-variant/20 bg-surface-container-lowest p-8 shadow-sm">
        <h3 class="flex items-center gap-2 font-serif text-xl text-on-surface">
            <LinkIcon class="h-5 w-5 text-primary" />
            Datos de Contacto del Hero
            <span class="ml-2 rounded-lg bg-surface-container-high px-2 py-0.5 text-xs font-bold uppercase tracking-widest text-primary"
                >Solo Consultoría</span
            >
        </h3>
        <p class="text-xs font-medium text-on-surface-variant">
            Estos datos aparecerán como accesos rápidos en el hero de la página <strong>/consultoria</strong>. Puedes dejar en blanco los que no
            desees mostrar.
        </p>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-2">
                <label class="ml-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant"
                    >Número WhatsApp (solo dígitos con código de país)</label
                >
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-on-surface-variant">+</span>
                    <input
                        v-model="slide.whatsapp_number"
                        type="text"
                        class="h-14 w-full rounded-2xl border border-outline-variant/30 bg-surface-container-high pl-8 pr-5 text-sm font-bold text-on-surface outline-none transition-all focus:border-primary focus:ring-primary"
                        placeholder="51999000000"
                    />
                </div>
                <p class="pl-2 text-[10px] text-on-surface-variant/60">Ej: 51961867761 (sin espacios ni guiones)</p>
            </div>

            <div class="space-y-2">
                <label class="ml-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant"
                    >Correo electrónico de contacto</label
                >
                <input
                    v-model="slide.contact_email"
                    type="email"
                    class="h-14 w-full rounded-2xl border border-outline-variant/30 bg-surface-container-high px-5 text-sm font-bold text-on-surface outline-none transition-all focus:border-primary focus:ring-primary"
                    placeholder="info@iee.edu.pe"
                />
            </div>

            <div class="space-y-2 md:col-span-2">
                <label class="ml-2 text-[10px] font-extrabold uppercase tracking-widest text-on-surface-variant">Dirección / Ubicación</label>
                <input
                    v-model="slide.contact_address"
                    type="text"
                    class="h-14 w-full rounded-2xl border border-outline-variant/30 bg-surface-container-high px-5 text-sm font-bold text-on-surface outline-none transition-all focus:border-primary focus:ring-primary"
                    placeholder="Trujillo, La Libertad — Perú"
                />
            </div>
        </div>
    </div>
</template>
