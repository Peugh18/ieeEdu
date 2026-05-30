<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Mail, X } from 'lucide-vue-next';

const props = defineProps<{
    emailVerifiedAt?: string | null;
}>();

const STORAGE_KEY = 'email_banner_dismissed_at';

const dismissedAt = ref<number | null>(null);

function getDismissedAt(): number | null {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return null;
    const parsed = parseInt(raw, 10);
    return isNaN(parsed) ? null : parsed;
}

dismissedAt.value = getDismissedAt();

const isVisible = computed(() => {
    if (props.emailVerifiedAt !== null && props.emailVerifiedAt !== undefined) {
        return false;
    }
    const dismissed = dismissedAt.value;
    if (!dismissed) return true;
    const hoursSinceDismiss = (Date.now() - dismissed) / (1000 * 60 * 60);
    return hoursSinceDismiss >= 24;
});

const resendForm = useForm({});

function resend() {
    resendForm.post(route('verification.send'));
}

function dismiss() {
    localStorage.setItem(STORAGE_KEY, String(Date.now()));
    dismissedAt.value = Date.now();
}
</script>

<template>
    <div v-if="isVisible" class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-4 mb-6">
        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
            <Mail class="w-5 h-5 text-amber-600" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-amber-900">
                Verifica tu correo para recibir notificaciones
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button
                @click="resend"
                :disabled="resendForm.processing"
                class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 hover:bg-amber-200 px-3 py-2 rounded-xl transition-colors disabled:opacity-50"
            >
                {{ resendForm.processing ? 'Enviando...' : 'Reenviar enlace' }}
            </button>
            <button
                @click="dismiss"
                class="p-2 text-amber-400 hover:text-amber-700 transition-colors"
                title="Descartar"
            >
                <X class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
