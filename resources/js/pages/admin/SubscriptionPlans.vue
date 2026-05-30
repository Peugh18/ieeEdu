<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Save, Loader2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface PlanItem {
    id: number;
    slug: string;
    name: string;
    price: number;
    months: number;
    period_label: string;
    badge: string | null;
    description: string | null;
    features: string[];
    sort_order: number;
    is_active: boolean;
}

const props = defineProps<{ plans: PlanItem[] }>();

const savingId = ref<number | null>(null);

function makeForm(plan: PlanItem) {
    return useForm({
        name: plan.name,
        price: plan.price,
        months: plan.months,
        period_label: plan.period_label,
        badge: plan.badge ?? '',
        description: plan.description ?? '',
        features_text: (plan.features ?? []).join('\n'),
        sort_order: plan.sort_order,
        is_active: plan.is_active,
    });
}

const forms = ref(Object.fromEntries(props.plans.map((plan) => [plan.id, makeForm(plan)])));

function savePlan(plan: PlanItem) {
    const form = forms.value[plan.id];
    savingId.value = plan.id;
    form.patch(route('admin.settings.plans.update', plan.id), {
        preserveScroll: true,
        onFinish: () => { savingId.value = null; },
    });
}
</script>

<template>
    <Head title="Planes Premium - Admin IEE" />
    <AppLayout>
        <div class="max-w-6xl mx-auto px-4 py-8 space-y-8">
            <AdminPageHeader
                title="Planes "
                title-accent="Premium"
                subtitle="Precios, duración y beneficios visibles en /planes."
                compact
            />

            <div class="grid gap-6">
                <article
                    v-for="plan in plans"
                    :key="plan.id"
                    class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden"
                >
                    <form @submit.prevent="savePlan(plan)" class="p-8 space-y-5">
                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ plan.slug }}</p>
                                <h2 class="text-xl font-bold text-slate-900">{{ forms[plan.id].name }}</h2>
                            </div>
                            <label class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-500">
                                <input v-model="forms[plan.id].is_active" type="checkbox" class="rounded border-slate-300" />
                                Activo
                            </label>
                        </div>

                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Nombre</label>
                                <input v-model="forms[plan.id].name" class="mt-1 w-full h-11 rounded-xl border border-slate-200 px-3 text-sm font-bold" />
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Precio (S/)</label>
                                <input v-model.number="forms[plan.id].price" type="number" step="0.01" min="0" class="mt-1 w-full h-11 rounded-xl border border-slate-200 px-3 text-sm font-bold" />
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Meses de vigencia</label>
                                <input v-model.number="forms[plan.id].months" type="number" min="1" class="mt-1 w-full h-11 rounded-xl border border-slate-200 px-3 text-sm font-bold" />
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Etiqueta periodo</label>
                                <input v-model="forms[plan.id].period_label" placeholder="3 meses" class="mt-1 w-full h-11 rounded-xl border border-slate-200 px-3 text-sm" />
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Etiqueta destacada</label>
                                <input v-model="forms[plan.id].badge" placeholder="Ideal para empezar" class="mt-1 w-full h-11 rounded-xl border border-slate-200 px-3 text-sm" />
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Orden</label>
                                <input v-model.number="forms[plan.id].sort_order" type="number" min="0" class="mt-1 w-full h-11 rounded-xl border border-slate-200 px-3 text-sm font-bold" />
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Descripción corta</label>
                            <textarea v-model="forms[plan.id].description" rows="2" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">¿Qué incluye? (un beneficio por línea)</label>
                            <textarea
                                v-model="forms[plan.id].features_text"
                                rows="8"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm leading-relaxed"
                                placeholder="Acceso ilimitado a cursos grabados&#10;Certificaciones incluidas"
                            ></textarea>
                            <p v-if="forms[plan.id].errors.features" class="text-xs text-rose-500 mt-1">{{ forms[plan.id].errors.features }}</p>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="savingId === plan.id"
                                class="inline-flex items-center gap-2 h-11 px-6 rounded-xl bg-primary text-white text-xs font-bold uppercase tracking-wider disabled:opacity-60"
                            >
                                <Loader2 v-if="savingId === plan.id" class="w-4 h-4 animate-spin" />
                                <Save v-else class="w-4 h-4" />
                                Guardar plan
                            </button>
                        </div>
                    </form>
                </article>
            </div>
        </div>
    </AppLayout>
</template>
