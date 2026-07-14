import { ref } from 'vue';

type ConfirmDialogState = {
    isOpen: boolean;
    title: string;
    description: string;
    confirmLabel: string;
    cancelLabel: string;
    danger: boolean;
    onConfirm: (() => void) | null;
    onCancel: (() => void) | null;
};

const state = ref<ConfirmDialogState>({
    isOpen: false,
    title: '',
    description: '',
    confirmLabel: 'Confirmar',
    cancelLabel: 'Cancelar',
    danger: false,
    onConfirm: null,
    onCancel: null,
});

export function useConfirmDialog() {
    const confirm = (options: {
        title: string;
        description: string;
        confirmLabel?: string;
        cancelLabel?: string;
        danger?: boolean;
    }): Promise<boolean> => {
        return new Promise((resolve) => {
            state.value = {
                isOpen: true,
                title: options.title,
                description: options.description,
                confirmLabel: options.confirmLabel ?? 'Confirmar',
                cancelLabel: options.cancelLabel ?? 'Cancelar',
                danger: options.danger ?? false,
                onConfirm: () => {
                    state.value.isOpen = false;
                    resolve(true);
                },
                onCancel: () => {
                    state.value.isOpen = false;
                    resolve(false);
                },
            };
        });
    };

    return {
        state,
        confirm,
    };
}
