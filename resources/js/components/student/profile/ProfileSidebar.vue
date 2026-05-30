<script setup lang="ts">
import { Crown, LogOut, Settings as SettingsIcon } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import type { Component } from 'vue';

interface Tab { id: string; label: string; icon: Component }

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
    <aside class="w-full lg:w-[300px] shrink-0 sticky top-24">
        <div class="bg-white border border-outline-variant/30 rounded-3xl shadow-sm p-6 space-y-6">
            <div class="flex flex-col items-center text-center space-y-3 pb-6 border-b border-outline-variant/20">
                <div class="relative group cursor-pointer" @click="emit('avatarClick')">
                    <img :src="user.avatar ? '/storage/'+user.avatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=57572A&color=fff`" class="w-24 h-24 rounded-full object-cover border-[6px] border-white shadow-xl bg-surface transition-opacity group-hover:opacity-75" @error="($event.target as HTMLImageElement).src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=57572A&color=fff`" />
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="bg-black/50 text-white rounded-full p-2"><SettingsIcon class="w-5 h-5" /></div>
                    </div>
                    <div v-if="hasSubscription" class="absolute -bottom-1 -right-1 bg-gradient-to-r from-amber-400 to-amber-600 text-white p-2 rounded-full ring-4 ring-white" title="Premium Activo"><Crown class="w-4 h-4" /></div>
                </div>
                <div class="mt-2">
                    <h3 class="font-serif font-bold text-xl text-on-surface">{{ user.name }}</h3>
                    <p class="text-xs text-on-surface-variant font-medium mt-1">{{ user.email }}</p>
                </div>
                <div class="flex gap-2 justify-center mt-2">
                    <span class="bg-green-100 text-green-700 font-bold text-[10px] uppercase tracking-widest px-3 py-1 rounded-full">Alumno Activo</span>
                </div>
            </div>
            <nav class="space-y-2">
                <button v-for="tab in tabs" :key="tab.id" @click="emit('update:activeTab', tab.id)" class="flex w-full items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all" :class="activeTab === tab.id ? 'bg-primary text-white shadow-md' : 'text-on-surface hover:bg-outline-variant/10'">
                    <component :is="tab.icon" class="w-5 h-5" :class="activeTab === tab.id ? 'text-white' : 'opacity-60'" />{{ tab.label }}
                </button>
            </nav>
            <div class="pt-6 border-t border-outline-variant/20">
                <Link :href="route('logout')" method="post" as="button" class="flex w-full items-center justify-center gap-2 text-red-600 hover:bg-red-50 p-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border border-red-100 transition-colors group">
                    <LogOut class="w-4 h-4 opacity-70 group-hover:opacity-100" />Cerrar Sesión
                </Link>
            </div>
        </div>
    </aside>
</template>
