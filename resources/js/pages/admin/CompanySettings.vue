<script setup lang="ts">
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Facebook, Instagram, Linkedin, Loader2, Mail, MapPin, MessageCircle, Music, Save, Twitter, Youtube } from 'lucide-vue-next';

interface CompanySettings {
    whatsapp_sales: string;
    whatsapp_support?: string | null;
    social_facebook?: string;
    social_linkedin?: string;
    social_instagram?: string;
    social_youtube?: string;
    social_tiktok?: string;
    social_twitter?: string;
    contact_email?: string;
    contact_address?: string;
}

const props = defineProps<{ settings: CompanySettings }>();

const form = useForm({
    whatsapp_sales: props.settings.whatsapp_sales,
    whatsapp_support: props.settings.whatsapp_support ?? '',
    social_facebook: props.settings.social_facebook ?? '',
    social_linkedin: props.settings.social_linkedin ?? '',
    social_instagram: props.settings.social_instagram ?? '',
    social_youtube: props.settings.social_youtube ?? '',
    social_tiktok: props.settings.social_tiktok ?? '',
    social_twitter: props.settings.social_twitter ?? '',
    contact_email: props.settings.contact_email ?? '',
    contact_address: props.settings.contact_address ?? '',
});

function submit() {
    form.patch(route('admin.settings.company.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Configuración de Empresa - Admin IEE" />
    <AppLayout>
        <div class="mx-auto max-w-4xl space-y-8 px-4 py-8">
            <AdminPageHeader title="Datos de " title-accent="empresa" subtitle="WhatsApp de ventas, redes sociales y contacto público." compact>
                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="inline-flex h-12 items-center gap-2.5 rounded-2xl bg-primary px-7 text-sm font-bold text-white shadow-xl disabled:opacity-70"
                >
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    <Save v-else class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                </button>
            </AdminPageHeader>

            <form @submit.prevent="submit" class="space-y-6">
                <section class="space-y-6 rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <div class="mb-2 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <MessageCircle class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">WhatsApp de ventas</h2>
                            <p class="text-sm text-slate-500">Usado en cursos, libros, membresías y carrito. Solo dígitos con código de país.</p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Número principal *</label>
                            <input
                                v-model="form.whatsapp_sales"
                                type="text"
                                placeholder="51959166911"
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm font-bold"
                            />
                            <p v-if="form.errors.whatsapp_sales" class="mt-1 text-xs text-rose-500">{{ form.errors.whatsapp_sales }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Soporte (opcional)</label>
                            <input
                                v-model="form.whatsapp_support"
                                type="text"
                                placeholder="51934057867"
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm font-bold"
                            />
                            <p v-if="form.errors.whatsapp_support" class="mt-1 text-xs text-rose-500">{{ form.errors.whatsapp_support }}</p>
                        </div>
                    </div>
                </section>

                <section class="space-y-6 rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <div class="mb-2 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <Facebook class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Redes sociales</h2>
                            <p class="text-sm text-slate-500">Enlaces del footer. No se mezclan con WhatsApp de ventas.</p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                ><Facebook class="h-3 w-3" /> Facebook</label
                            >
                            <input
                                v-model="form.social_facebook"
                                type="url"
                                placeholder="https://facebook.com/..."
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm"
                            />
                            <p v-if="form.errors.social_facebook" class="mt-1 text-xs text-rose-500">{{ form.errors.social_facebook }}</p>
                        </div>
                        <div>
                            <label class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                ><Linkedin class="h-3 w-3" /> LinkedIn</label
                            >
                            <input
                                v-model="form.social_linkedin"
                                type="url"
                                placeholder="https://linkedin.com/company/..."
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm"
                            />
                            <p v-if="form.errors.social_linkedin" class="mt-1 text-xs text-rose-500">{{ form.errors.social_linkedin }}</p>
                        </div>
                        <div>
                            <label class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                ><Instagram class="h-3 w-3" /> Instagram</label
                            >
                            <input
                                v-model="form.social_instagram"
                                type="url"
                                placeholder="https://instagram.com/..."
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm"
                            />
                            <p v-if="form.errors.social_instagram" class="mt-1 text-xs text-rose-500">{{ form.errors.social_instagram }}</p>
                        </div>
                        <div>
                            <label class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                ><Youtube class="h-3 w-3" /> YouTube</label
                            >
                            <input
                                v-model="form.social_youtube"
                                type="url"
                                placeholder="https://youtube.com/..."
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm"
                            />
                            <p v-if="form.errors.social_youtube" class="mt-1 text-xs text-rose-500">{{ form.errors.social_youtube }}</p>
                        </div>
                        <div>
                            <label class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                ><Music class="h-3 w-3" /> TikTok</label
                            >
                            <input
                                v-model="form.social_tiktok"
                                type="url"
                                placeholder="https://tiktok.com/@..."
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm"
                            />
                            <p v-if="form.errors.social_tiktok" class="mt-1 text-xs text-rose-500">{{ form.errors.social_tiktok }}</p>
                        </div>
                        <div>
                            <label class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                ><Twitter class="h-3 w-3" /> Twitter / X</label
                            >
                            <input
                                v-model="form.social_twitter"
                                type="url"
                                placeholder="https://x.com/..."
                                class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm"
                            />
                            <p v-if="form.errors.social_twitter" class="mt-1 text-xs text-rose-500">{{ form.errors.social_twitter }}</p>
                        </div>
                    </div>
                </section>

                <section class="space-y-5 rounded-[2rem] border border-slate-100 bg-white p-8 shadow-sm">
                    <div class="mb-2 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <Mail class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Contacto público</h2>
                            <p class="text-sm text-slate-500">Se muestra en el footer y como respaldo en consultoría.</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Correo</label>
                        <input v-model="form.contact_email" type="email" class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm" />
                    </div>
                    <div>
                        <label class="flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                            ><MapPin class="h-3 w-3" /> Dirección</label
                        >
                        <input v-model="form.contact_address" type="text" class="mt-2 h-12 w-full rounded-xl border border-slate-200 px-4 text-sm" />
                    </div>
                </section>
            </form>
        </div>
    </AppLayout>
</template>
