<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Crown, LogOut, Settings as SettingsIcon } from 'lucide-vue-next';
import type { Component } from 'vue';

interface Tab {
    id: string;
    label: string;
    icon: Component;
}

defineProps<{
    tabs: Tab[];
    activeTab: string;
    user: { name: string; email: string; avatar?: string };
    hasSubscription?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:activeTab', tab: string): void;
    (e: 'avatarClick'): void;
}>();
</script>

<template>
    <aside class="sticky top-24 w-full shrink-0 lg:w-[300px]">
        <div class="space-y-6 rounded-3xl border border-outline-variant/30 bg-white p-6 shadow-sm">
            <div class="flex flex-col items-center space-y-3 border-b border-outline-variant/20 pb-6 text-center">
                <div class="group relative cursor-pointer" @click="emit('avatarClick')">
                    <img
                        :src="
                            user.avatar
                                ? '/storage/' + user.avatar
                                : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=57572A&color=fff`
                        "
                        class="h-24 w-24 rounded-full border-[6px] border-white bg-surface object-cover shadow-xl transition-opacity group-hover:opacity-75"
                        @error="
                            ($event.target as HTMLImageElement).src =
                                `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=57572A&color=fff`
                        "
                    />
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity group-hover:opacity-100">
                        <div class="rounded-full bg-black/50 p-2 text-white"><SettingsIcon class="h-5 w-5" /></div>
                    </div>
                    <div
                        v-if="hasSubscription"
                        class="absolute -bottom-1 -right-1 rounded-full bg-gradient-to-r from-amber-400 to-amber-600 p-2 text-white ring-4 ring-white"
                        title="Premium Activo"
                    >
                        <Crown class="h-4 w-4" />
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="font-serif text-xl font-bold text-on-surface">{{ user.name }}</h3>
                    <p class="mt-1 text-xs font-medium text-on-surface-variant">{{ user.email }}</p>
                </div>
                <div class="mt-2 flex justify-center gap-2">
                    <span class="rounded-full bg-green-100 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-green-700"
                        >Alumno Activo</span
                    >
                </div>
            </div>
            <nav class="space-y-2">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="emit('update:activeTab', tab.id)"
                    class="flex w-full items-center gap-3 rounded-2xl px-4 py-3.5 text-sm font-bold transition-all"
                    :class="activeTab === tab.id ? 'bg-primary text-white shadow-md' : 'text-on-surface hover:bg-outline-variant/10'"
                >
                    <component :is="tab.icon" class="h-5 w-5" :class="activeTab === tab.id ? 'text-white' : 'opacity-60'" />{{ tab.label }}
                </button>
            </nav>
            <div class="border-t border-outline-variant/20 pt-6">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="group flex w-full items-center justify-center gap-2 rounded-2xl border border-red-100 p-3.5 text-xs font-bold uppercase tracking-widest text-red-600 transition-colors hover:bg-red-50"
                >
                    <LogOut class="h-4 w-4 opacity-70 group-hover:opacity-100" />Cerrar Sesión
                </Link>
            </div>
        </div>
    </aside>
</template>
