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
    <section class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div>
            <h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Seguridad de la Cuenta</h2>
            <p class="text-on-surface-variant text-sm">Actualiza tu contraseña para mantener tu cuenta protegida.</p>
        </div>
        <form @submit.prevent="emit('submit')" class="space-y-6 max-w-xl bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-8">
            <div class="space-y-2">
                <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Nueva Contraseña</label>
                <input :value="passwordForm.password" @input="emit('update:password', 'password', ($event.target as HTMLInputElement).value)" type="password" placeholder="Mínimo 8 caracteres" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" />
                <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Confirmar Contraseña</label>
                <input :value="passwordForm.password_confirmation" @input="emit('update:password', 'password_confirmation', ($event.target as HTMLInputElement).value)" type="password" placeholder="Repite tu nueva contraseña" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all" />
            </div>
            <div class="pt-4">
                <button type="submit" :disabled="passwordForm.processing" class="inline-flex bg-primary text-white font-bold text-xs uppercase tracking-widest px-8 py-3 rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50">{{ passwordForm.processing ? 'Actualizando...' : 'Actualizar Contraseña' }}</button>
            </div>
        </form>
        <div class="mt-8">
            <h3 class="text-xl font-serif font-bold text-on-surface mb-6">Apariencia Visual</h3>
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-8 max-w-xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Tema de la Interfaz</p>
                    <p class="text-xs text-on-surface-variant/80 mt-1">Escoge tu modo preferido.</p>
                </div>
                <AppearanceTabs />
            </div>
        </div>
    </section>
</template>
