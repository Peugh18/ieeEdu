<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface CategoryItem {
    id: number;
    name: string;
    description: string;
}

const props = defineProps<{ categories: { data: CategoryItem[]; links: any[] } }>();

const form = useForm({ id: null as number | null, name: '', description: '' });
const editing = ref(false);

function setEdit(category: CategoryItem) {
    editing.value = true;
    form.id = category.id;
    form.name = category.name;
    form.description = category.description;
}

function submit() {
    if (editing.value && form.id) {
        form.put(route('admin.categories.update', form.id), {
            onSuccess: () => {
                editing.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => form.reset(),
        });
    }
}

function cancel() {
    editing.value = false;
    form.reset();
}

function destroy(category: CategoryItem) {
    if (!confirm('Eliminar categoría?')) return;
    useForm().delete(route('admin.categories.destroy', category.id), { preserveState: true });
}
</script>

<template>
    <Head title="Admin Categorías" />
    <AppLayout>
        <div class="mb-6 rounded-lg border p-4">
            <h2 class="text-lg font-semibold">{{ editing ? 'Editar categoría' : 'Crear categoría' }}</h2>
            <form @submit.prevent="submit" class="mt-4 grid gap-2">
                <input v-model="form.name" placeholder="Nombre" required class="w-full rounded border p-2" />
                <textarea v-model="form.description" placeholder="Descripción" rows="3" class="w-full rounded border p-2"></textarea>
                <div class="flex gap-2">
                    <button class="rounded bg-green-600 px-4 py-2 text-white">{{ editing ? 'Guardar' : 'Crear' }}</button>
                    <button v-if="editing" type="button" class="rounded bg-gray-500 px-4 py-2 text-white" @click="cancel">Cancelar</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg border">
            <table class="w-full border-collapse">
                <thead class="bg-slate-100 text-left text-sm font-medium">
                    <tr>
                        <th class="px-3 py-2">Nombre</th>
                        <th class="px-3 py-2">Descripción</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in props.categories.data" :key="category.id" class="border-t">
                        <td class="px-3 py-2">{{ category.name }}</td>
                        <td class="px-3 py-2">{{ category.description }}</td>
                        <td class="px-3 py-2">
                            <button class="rounded bg-blue-600 px-2 py-1 text-white" @click="setEdit(category)">Editar</button>
                            <button class="rounded bg-red-600 px-2 py-1 text-white" @click="destroy(category)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <ul class="flex gap-2">
                <li v-for="link in props.categories.links" :key="link.label">
                    <a v-if="link.url" :href="link.url" class="rounded border px-3 py-1">{{ link.label }}</a>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
