<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface UserItem {
    id: number;
    name: string;
    email: string;
    telefono: string | null;
    role: string;
    status: string;
}

const props = defineProps<{
    users: {
        data: UserItem[];
        links: any[];
    };
    filters: {
        role?: string;
        status?: string;
        search?: string;
    };
}>();

const createForm = useForm({
    name: '',
    email: '',
    telefono: '',
    role: 'usuario',
    status: 'activo',
    password: '',
});

const editForm = useForm({
    id: null as number | null,
    name: '',
    email: '',
    telefono: '',
    role: 'usuario',
    status: 'activo',
    password: '',
});

const filtersForm = useForm({
    search: props.filters.search || '',
    role: props.filters.role || '',
    status: props.filters.status || '',
});

const editing = ref(false);

function applyFilters() {
    filtersForm.get(route('admin.users.index'), { preserveState: true, replace: true });
}

function submitCreate() {
    createForm.post(route('admin.users.store'), {
        onSuccess: () => {
            createForm.reset();
        },
    });
}

function setEdit(user: UserItem) {
    editing.value = true;
    editForm.reset();
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.telefono = user.telefono || '';
    editForm.role = user.role;
    editForm.status = user.status;
}

function submitEdit() {
    if (!editForm.id) return;
    editForm.put(route('admin.users.update', editForm.id), {
        onSuccess: () => {
            editing.value = false;
            editForm.reset();
        },
    });
}

function cancelEdit() {
    editing.value = false;
    editForm.reset();
}

function destroyUser(user: UserItem) {
    if (!window.confirm('¿Eliminar usuario?')) return;
    useForm().delete(route('admin.users.destroy', user.id), {
        preserveState: true,
    });
}

const roles = ['admin', 'usuario'];
const statuses = ['activo', 'inactivo'];

const usersList = computed(() => props.users.data);
const userLinks = computed(() => props.users.links.filter((link: any) => link.url));
</script>

<template>
    <Head title="Admin Usuarios" />

    <AppLayout>
        <div class="mb-6 grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border p-4">
                <h2 class="text-lg font-semibold">Filtrar</h2>
                <div class="mt-3 grid gap-2">
                    <input
                        v-model="filtersForm.search"
                        placeholder="Buscar por nombre o email"
                        class="w-full rounded border p-2"
                    />
                    <select v-model="filtersForm.role" class="w-full rounded border p-2">
                        <option value="">Todos los roles</option>
                        <option value="admin">admin</option>
                        <option value="usuario">usuario</option>
                    </select>
                    <select v-model="filtersForm.status" class="w-full rounded border p-2">
                        <option value="">Todos los estados</option>
                        <option value="activo">activo</option>
                        <option value="inactivo">inactivo</option>
                    </select>
                    <button class="rounded bg-slate-800 px-3 py-2 text-white" @click.prevent="applyFilters">Aplicar</button>
                </div>
            </div>

            <div class="rounded-lg border p-4">
                <h2 class="text-lg font-semibold">{{ editing ? 'Editar usuario' : 'Crear usuario' }}</h2>
                <form v-if="editing" @submit.prevent="submitEdit" class="mt-3 grid gap-2">
                    <input v-model="editForm.name" placeholder="Nombre" required class="w-full rounded border p-2" />
                    <input v-model="editForm.email" type="email" placeholder="Email" required class="w-full rounded border p-2" />
                    <input v-model="editForm.telefono" placeholder="Teléfono" class="w-full rounded border p-2" />

                    <select v-model="editForm.role" class="w-full rounded border p-2">
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                    <select v-model="editForm.status" class="w-full rounded border p-2">
                        <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                    </select>
                    <input v-model="editForm.password" type="password" placeholder="Password" class="w-full rounded border p-2" />
                    <div class="flex gap-2">
                        <button type="submit" class="rounded bg-green-600 px-4 py-2 text-white">Guardar</button>
                        <button type="button" class="rounded bg-gray-500 px-4 py-2 text-white" @click="cancelEdit">Cancelar</button>
                    </div>
                </form>
                <form v-else @submit.prevent="submitCreate" class="mt-3 grid gap-2">
                    <input v-model="createForm.name" placeholder="Nombre" required class="w-full rounded border p-2" />
                    <input v-model="createForm.email" type="email" placeholder="Email" required class="w-full rounded border p-2" />
                    <input v-model="createForm.telefono" placeholder="Teléfono" class="w-full rounded border p-2" />

                    <select v-model="createForm.role" class="w-full rounded border p-2">
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                    <select v-model="createForm.status" class="w-full rounded border p-2">
                        <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                    </select>
                    <input v-model="createForm.password" type="password" placeholder="Password" required class="w-full rounded border p-2" />
                    <div class="flex gap-2">
                        <button type="submit" class="rounded bg-green-600 px-4 py-2 text-white">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-left text-sm font-medium">
                        <th class="px-3 py-2">Nombre</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Teléfono</th>
                        <th class="px-3 py-2">Rol</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in usersList" :key="user.id" class="border-t">
                        <td class="px-3 py-2">{{ user.name }}</td>
                        <td class="px-3 py-2">{{ user.email }}</td>
                        <td class="px-3 py-2">{{ user.telefono || '-' }}</td>
                        <td class="px-3 py-2">{{ user.role }}</td>
                        <td class="px-3 py-2">{{ user.status }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <button class="rounded bg-blue-600 px-2 py-1 text-white" @click="setEdit(user)">Editar</button>
                            <button class="rounded bg-red-600 px-2 py-1 text-white" @click="destroyUser(user)">Eliminar</button>
                        </td>
                    </tr>
                    <tr v-if="usersList.length === 0"><td colspan="6" class="p-4 text-center">Sin usuarios</td></tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3 flex justify-between">
            <ul class="flex gap-2">
                <li v-for="link in userLinks" :key="link.label">
                    <Link :href="link.url" class="rounded border px-3 py-1">{{ link.label }}</Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
