<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Mail, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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
    <div v-if="isVisible" class="mb-6 flex items-center gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-4">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-amber-100">
            <Mail class="h-5 w-5 text-amber-600" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-amber-900">Verifica tu correo para recibir notificaciones</p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <button
                @click="resend"
                :disabled="resendForm.processing"
                class="rounded-xl bg-amber-100 px-3 py-2 text-xs font-bold uppercase tracking-wider text-amber-800 transition-colors hover:bg-amber-200 disabled:opacity-50"
            >
                {{ resendForm.processing ? 'Enviando...' : 'Reenviar enlace' }}
            </button>
            <button @click="dismiss" class="p-2 text-amber-400 transition-colors hover:text-amber-700" title="Descartar">
                <X class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>
