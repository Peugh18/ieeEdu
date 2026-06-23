<script setup lang="ts">
import AppearanceTabs from '@/components/AppearanceTabs.vue';

defineProps<{
    passwordForm: { password: string; password_confirmation: string; processing: boolean; errors: Record<string, string>; wasSuccessful?: boolean };
}>();

const emit = defineEmits<{
    (e: 'update:password', field: 'password' | 'password_confirmation', value: string): void;
    (e: 'submit'): void;
}>();
</script>

<template>
    <section class="space-y-10 duration-500 animate-in fade-in slide-in-from-bottom-4">
        <div>
            <h2 class="mb-2 font-serif text-3xl font-bold text-on-surface">Seguridad de la Cuenta</h2>
            <p class="text-sm text-on-surface-variant">Actualiza tu contraseña para mantener tu cuenta protegida.</p>
        </div>
        <form
            @submit.prevent="emit('submit')"
            class="max-w-xl space-y-6 rounded-3xl border border-outline-variant/30 bg-surface-container-lowest p-8"
        >
            <div class="space-y-2">
                <label class="text-sm font-bold uppercase tracking-widest text-on-surface-variant">Nueva Contraseña</label>
                <input
                    :value="passwordForm.password"
                    @input="emit('update:password', 'password', ($event.target as HTMLInputElement).value)"
                    type="password"
                    placeholder="Mínimo 8 caracteres"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface px-4 py-3 text-sm transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary"
                />
                <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-red-500">{{ passwordForm.errors.password }}</p>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold uppercase tracking-widest text-on-surface-variant">Confirmar Contraseña</label>
                <input
                    :value="passwordForm.password_confirmation"
                    @input="emit('update:password', 'password_confirmation', ($event.target as HTMLInputElement).value)"
                    type="password"
                    placeholder="Repite tu nueva contraseña"
                    class="w-full rounded-xl border border-outline-variant/30 bg-surface px-4 py-3 text-sm transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>
            <div class="pt-4">
                <button
                    type="submit"
                    :disabled="passwordForm.processing"
                    class="inline-flex rounded-xl bg-primary px-8 py-3 text-xs font-bold uppercase tracking-widest text-white transition-opacity hover:opacity-90 disabled:opacity-50"
                >
                    {{ passwordForm.processing ? 'Actualizando...' : 'Actualizar Contraseña' }}
                </button>
            </div>
        </form>
        <div class="mt-8">
            <h3 class="mb-6 font-serif text-xl font-bold text-on-surface">Apariencia Visual</h3>
            <div class="flex max-w-xl items-center justify-between rounded-3xl border border-outline-variant/30 bg-surface-container-lowest p-8">
                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-on-surface-variant">Tema de la Interfaz</p>
                    <p class="mt-1 text-xs text-on-surface-variant/80">Escoge tu modo preferido.</p>
                </div>
                <AppearanceTabs />
            </div>
        </div>
    </section>
</template>
