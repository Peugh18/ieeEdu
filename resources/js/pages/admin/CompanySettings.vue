<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { MessageCircle, Facebook, Linkedin, Mail, MapPin, Save, Loader2 } from 'lucide-vue-next';

interface CompanySettings {
    whatsapp_sales: string;
    whatsapp_support?: string | null;
    social_facebook?: string;
    social_linkedin?: string;
    contact_email?: string;
    contact_address?: string;
}

const props = defineProps<{ settings: CompanySettings }>();

const form = useForm({
    whatsapp_sales: props.settings.whatsapp_sales,
    whatsapp_support: props.settings.whatsapp_support ?? '',
    social_facebook: props.settings.social_facebook ?? '',
    social_linkedin: props.settings.social_linkedin ?? '',
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
        <div class="max-w-4xl mx-auto px-4 py-8 space-y-8">
            <AdminPageHeader
                title="Datos de "
                title-accent="empresa"
                subtitle="WhatsApp de ventas, redes sociales y contacto público."
                compact
            >
                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="h-12 inline-flex items-center gap-2.5 rounded-2xl px-7 text-sm font-bold shadow-xl bg-primary text-white disabled:opacity-70"
                >
                    <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                    <Save v-else class="h-4 w-4" />
                    {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                </button>
            </AdminPageHeader>

            <form @submit.prevent="submit" class="space-y-6">
                <section class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <MessageCircle class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">WhatsApp de ventas</h2>
                            <p class="text-sm text-slate-500">Usado en cursos, libros, membresías y carrito. Solo dígitos con código de país.</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Número principal *</label>
                            <input v-model="form.whatsapp_sales" type="text" placeholder="51959166911" class="mt-2 w-full h-12 rounded-xl border border-slate-200 px-4 text-sm font-bold" />
                            <p v-if="form.errors.whatsapp_sales" class="text-xs text-rose-500 mt-1">{{ form.errors.whatsapp_sales }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Soporte (opcional)</label>
                            <input v-model="form.whatsapp_support" type="text" placeholder="51934057867" class="mt-2 w-full h-12 rounded-xl border border-slate-200 px-4 text-sm font-bold" />
                            <p v-if="form.errors.whatsapp_support" class="text-xs text-rose-500 mt-1">{{ form.errors.whatsapp_support }}</p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <Facebook class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Redes sociales</h2>
                            <p class="text-sm text-slate-500">Enlaces del footer. No se mezclan con WhatsApp de ventas.</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 flex items-center gap-1"><Facebook class="w-3 h-3" /> Facebook</label>
                            <input v-model="form.social_facebook" type="url" placeholder="https://facebook.com/..." class="mt-2 w-full h-12 rounded-xl border border-slate-200 px-4 text-sm" />
                            <p v-if="form.errors.social_facebook" class="text-xs text-rose-500 mt-1">{{ form.errors.social_facebook }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 flex items-center gap-1"><Linkedin class="w-3 h-3" /> LinkedIn</label>
                            <input v-model="form.social_linkedin" type="url" placeholder="https://linkedin.com/company/..." class="mt-2 w-full h-12 rounded-xl border border-slate-200 px-4 text-sm" />
                            <p v-if="form.errors.social_linkedin" class="text-xs text-rose-500 mt-1">{{ form.errors.social_linkedin }}</p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <Mail class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Contacto público</h2>
                            <p class="text-sm text-slate-500">Se muestra en el footer y como respaldo en consultoría.</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Correo</label>
                        <input v-model="form.contact_email" type="email" class="mt-2 w-full h-12 rounded-xl border border-slate-200 px-4 text-sm" />
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 flex items-center gap-1"><MapPin class="w-3 h-3" /> Dirección</label>
                        <input v-model="form.contact_address" type="text" class="mt-2 w-full h-12 rounded-xl border border-slate-200 px-4 text-sm" />
                    </div>
                </section>
            </form>
        </div>
    </AppLayout>
</template>
