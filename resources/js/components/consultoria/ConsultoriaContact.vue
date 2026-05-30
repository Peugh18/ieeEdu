<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SharedData } from '@/types';

const props = defineProps<{
    areas: string[];
    heroEmail: string;
    heroAddress: string;
    whatsappLink: string;
}>();

const form = useForm({ name: '', email: '', phone: '', company: '', area: '', message: '' });
const page = usePage<SharedData>();
const flashSuccess = computed(() => page.props.flash?.success);

function submit() {
    form.post(route('consultoria.store'), { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <section id="solicitar" class="py-8 md:py-24 bg-surface relative overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/[0.03] rounded-full blur-[120px] pointer-events-none -translate-y-1/3 translate-x-1/4"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-px w-8 bg-primary/40"></div>
                <span class="text-[11px] font-bold text-primary tracking-[0.22em] uppercase">Contáctanos</span>
            </div>
            <h2 class="font-serif text-3xl md:text-4xl text-on-surface mb-5 md:mb-12">
                Envíanos un <em class="not-italic text-primary">mensaje</em>
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16 items-start">
                <div class="hidden lg:block lg:col-span-2 space-y-4">
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-surface-container border border-outline-variant/15 hover:border-primary/20 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/15 transition-colors">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div><p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-1">Dirección</p><p class="text-sm font-semibold text-on-surface">{{ heroAddress }}</p></div>
                    </div>
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-surface-container border border-outline-variant/15 hover:border-primary/20 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/15 transition-colors">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div><p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-1">Correo electrónico</p><a :href="`mailto:${heroEmail}`" class="text-sm font-semibold text-primary hover:underline underline-offset-2">{{ heroEmail }}</a></div>
                    </div>
                    <div class="flex items-start gap-4 p-5 rounded-2xl bg-green-500/[0.06] border border-green-500/15 hover:border-green-500/25 transition-colors group">
                        <div class="w-10 h-10 rounded-xl bg-green-500/15 flex items-center justify-center flex-shrink-0 group-hover:bg-green-500/25 transition-colors">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <div><p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest mb-1">¿Prefieres hablar directo?</p><a :href="whatsappLink" target="_blank" rel="noopener" class="text-sm font-bold text-green-600 hover:underline underline-offset-2">Escríbenos por WhatsApp →</a></div>
                    </div>
                    <p class="text-xs text-on-surface-variant/40 leading-relaxed px-1 pt-2">Respondemos en un máximo de <strong class="text-on-surface-variant/60">24 horas hábiles</strong>. Tus datos son completamente confidenciales.</p>
                </div>

                <div class="lg:col-span-3">
                    <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                        <div v-if="flashSuccess" class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-green-700 font-semibold">{{ flashSuccess }}</p>
                        </div>
                    </transition>

                    <form @submit.prevent="submit" class="space-y-4">
                        <input v-model="form.name" type="text" placeholder="Nombre completo *" class="elite-input w-full placeholder:text-on-surface-variant/35 text-sm transition-all focus:outline-none" :class="form.errors.name ? '!border-red-500 focus:!border-red-500 focus:!ring-red-500/20' : ''" />
                        <p v-if="form.errors.name" class="text-xs text-red-500 -mt-2 pl-1">{{ form.errors.name }}</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <input v-model="form.email" type="email" placeholder="Correo electrónico *" class="elite-input w-full placeholder:text-on-surface-variant/35 text-sm transition-all focus:outline-none" :class="form.errors.email ? '!border-red-500 focus:!border-red-500 focus:!ring-red-500/20' : ''" />
                                <p v-if="form.errors.email" class="text-xs text-red-500 mt-1 pl-1">{{ form.errors.email }}</p>
                            </div>
                            <input v-model="form.phone" type="tel" placeholder="Teléfono / WhatsApp" class="elite-input w-full placeholder:text-on-surface-variant/35 text-sm transition-all focus:outline-none" />
                        </div>
                        <input v-model="form.company" type="text" placeholder="Empresa o institución" class="elite-input w-full placeholder:text-on-surface-variant/35 text-sm transition-all focus:outline-none" />
                        <div class="relative">
                            <select v-model="form.area" class="elite-input w-full text-sm transition-all appearance-none pr-10 focus:outline-none" :class="[form.area ? 'text-on-surface' : 'text-on-surface-variant/35', form.errors.area ? '!border-red-500 focus:!border-red-500 focus:!ring-red-500/20' : '']">
                                <option value="" disabled>Área de consultoría de interés *</option>
                                <option v-for="area in areas" :key="area" :value="area" class="text-on-surface bg-surface">{{ area }}</option>
                            </select>
                            <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-variant/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            <p v-if="form.errors.area" class="text-xs text-red-500 mt-1 pl-1">{{ form.errors.area }}</p>
                        </div>
                        <textarea v-model="form.message" rows="4" placeholder="Tu mensaje *" class="elite-input w-full placeholder:text-on-surface-variant/35 text-sm transition-all resize-none focus:outline-none" :class="form.errors.message ? '!border-red-500 focus:!border-red-500 focus:!ring-red-500/20' : ''"></textarea>
                        <p v-if="form.errors.message" class="text-xs text-red-500 -mt-2 pl-1">{{ form.errors.message }}</p>
                        <button type="submit" :disabled="form.processing" class="elite-btn-primary w-full flex items-center justify-center gap-2.5 disabled:opacity-60 disabled:translate-y-0 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            {{ form.processing ? 'Enviando...' : 'ENVIAR MENSAJE' }}
                        </button>
                    </form>

                    <div class="md:hidden flex flex-col gap-2 mt-4">
                        <p class="text-xs text-on-surface-variant/50 text-center mb-1">¿Prefieres contactar directo?</p>
                        <a :href="whatsappLink" target="_blank" rel="noopener" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-green-500 text-white font-bold shadow-md shadow-green-500/20">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span class="flex-1 text-sm">Escríbenos por WhatsApp</span>
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <div class="grid grid-cols-2 gap-2">
                            <a :href="`mailto:${heroEmail}`" class="flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-surface-container border border-outline-variant/20">
                                <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-medium text-on-surface truncate">{{ heroEmail }}</span>
                            </a>
                            <div class="flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-surface-container border border-outline-variant/20">
                                <svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-xs font-medium text-on-surface">Trujillo, Perú</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
