<script setup lang="ts">
import type { NextLiveClass } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { Calendar, ExternalLink, X } from 'lucide-vue-next';

defineProps<{
    show: boolean;
    nextLiveClass: NextLiveClass | null;
    isLive: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show && nextLiveClass"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
                @click.self="emit('close')"
            >
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="scale-95 opacity-0 translate-y-4"
                    enter-to-class="scale-100 opacity-100 translate-y-0"
                >
                    <div v-show="show && nextLiveClass" class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">
                        <div class="relative overflow-hidden bg-gradient-to-br from-[#2C2C15] to-[#1a1a0a] p-8 text-white">
                            <div
                                class="absolute inset-0 opacity-10"
                                style="
                                    background-image: url(&quot;data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E&quot;);
                                "
                            ></div>
                            <div class="absolute right-0 top-0 h-40 w-40 rounded-full bg-[#D4AF37]/10 blur-[60px]"></div>

                            <button
                                @click="emit('close')"
                                class="absolute right-5 top-5 rounded-xl bg-white/10 p-2 text-white transition-colors hover:bg-white/20"
                            >
                                <X class="h-5 w-5" />
                            </button>

                            <div class="relative z-10">
                                <div class="mb-4 flex items-center gap-2">
                                    <span
                                        v-if="isLive"
                                        class="flex items-center gap-1.5 rounded-full border border-red-500/30 bg-red-500/20 px-3 py-1"
                                    >
                                        <span class="h-2 w-2 animate-pulse rounded-full bg-red-400"></span>
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-red-300">En Vivo Ahora</span>
                                    </span>
                                    <span v-else class="flex items-center gap-1.5 rounded-full border border-[#D4AF37]/20 bg-[#D4AF37]/10 px-3 py-1">
                                        <Calendar class="h-3 w-3 text-[#D4AF37]" />
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#D4AF37]">Sesión Programada</span>
                                    </span>
                                </div>
                                <h4 class="text-xl font-bold leading-snug">{{ nextLiveClass.title }}</h4>
                                <p class="mt-1 text-sm text-white/50">{{ nextLiveClass.course_title }}</p>
                            </div>
                        </div>

                        <div class="space-y-6 p-8">
                            <div class="space-y-2 rounded-2xl border border-gray-100 bg-gray-50 p-5">
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Fecha y hora</p>
                                <p class="text-sm font-semibold text-gray-800">{{ nextLiveClass.start_time_human }}</p>
                            </div>

                            <p class="text-sm leading-relaxed text-gray-500">
                                Dirígete a la página del curso para encontrar el enlace directo, materiales de apoyo y el chat de la sesión en vivo.
                            </p>

                            <div class="flex flex-col gap-3">
                                <Link
                                    :href="route('student.classroom', { course: nextLiveClass.course_slug })"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-4 text-xs font-bold uppercase tracking-widest text-white shadow-lg transition-all hover:bg-[#4a4a24] active:scale-95"
                                >
                                    Ir al Aula Virtual <ExternalLink class="h-4 w-4" />
                                </Link>
                                <button
                                    @click="emit('close')"
                                    class="w-full py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400 transition-colors hover:text-gray-600"
                                >
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
