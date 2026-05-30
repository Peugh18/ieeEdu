import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import type { SubscriptionUser } from '@/types/subscription';

export function useSubscriptionForm() {
    const showModal = ref(false);
    const usersResults = ref<SubscriptionUser[]>([]);
    const searchUserQuery = ref('');
    const isSearchingUser = ref(false);
    const showUserDropdown = ref(false);

    const form = useForm({
        user_id: '',
        type: 'trimestral',
        months: 3,
        amount: 350,
        comprobante: null as File | null,
    });

    watch(() => form.type, (newType) => {
        if (newType === 'trimestral') { form.months = 3; form.amount = 350; }
        else if (newType === 'semestral') { form.months = 6; form.amount = 600; }
        else if (newType === 'anual') { form.months = 12; form.amount = 990; }
    });

    function onFileChange(e: Event) {
        const el = e.target as HTMLInputElement;
        form.comprobante = el.files?.[0] ?? null;
    }

    function createPreviewUrl(file: File) {
        return URL.createObjectURL(file);
    }

    async function searchUsers() {
        if (searchUserQuery.value.length < 2) { usersResults.value = []; showUserDropdown.value = false; return; }
        isSearchingUser.value = true;
        try {
            const res = await axios.get(route('admin.users.search'), { params: { q: searchUserQuery.value } });
            usersResults.value = res.data;
            showUserDropdown.value = true;
        } catch (e) { console.error(e); } finally { isSearchingUser.value = false; }
    }

    function selectUser(user: SubscriptionUser) {
        form.user_id = String(user.id);
        searchUserQuery.value = `${user.name} (${user.email})`;
        showUserDropdown.value = false;
        usersResults.value = [];
    }

    function submit(onSuccess: () => void) {
        form.post(route('admin.subscriptions.store'), {
            forceFormData: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
                searchUserQuery.value = '';
                onSuccess();
            },
        });
    }

    function open() {
        form.reset();
        searchUserQuery.value = '';
        usersResults.value = [];
        showUserDropdown.value = false;
        showModal.value = true;
    }

    return {
        showModal,
        usersResults,
        searchUserQuery,
        isSearchingUser,
        showUserDropdown,
        form,
        onFileChange,
        createPreviewUrl,
        searchUsers,
        selectUser,
        submit,
        open,
    };
}
