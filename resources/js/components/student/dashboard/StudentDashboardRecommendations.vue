<script setup lang="ts">
import type { Recommendation } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Sparkles } from 'lucide-vue-next';

defineProps<{
    recommendations: Recommendation[];
}>();
</script>

<template>
    <div v-if="recommendations.length > 0" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-bold text-gray-900">
                <Sparkles class="h-5 w-5 text-primary" />
                Recomendado para ti
            </h2>
            <Link
                :href="route('student.explore.courses')"
                class="flex items-center gap-1 text-[11px] font-bold uppercase tracking-widest text-primary hover:underline"
            >
                Explorar catálogo <ArrowRight class="h-3 w-3" />
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article
                v-for="rec in recommendations"
                :key="rec.id"
                class="duration-400 group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white transition-all hover:-translate-y-1 hover:shadow-xl"
            >
                <div class="relative h-36 overflow-hidden bg-gray-100">
                    <img
                        v-if="rec.image"
                        :src="rec.image"
                        :alt="rec.title"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 to-transparent"></div>
                    <div class="absolute bottom-3 left-3">
                        <span
                            class="rounded-full bg-white/90 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-primary backdrop-blur-md"
                        >
                            {{ rec.category?.name }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <h4 class="mb-4 line-clamp-2 text-sm font-bold leading-snug text-gray-900 transition-colors group-hover:text-primary">
                        {{ rec.title }}
                    </h4>
                    <Link
                        :href="route('cursos.show', { slug: rec.slug, dashboard: true })"
                        class="mt-auto inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-primary transition-all hover:gap-2.5"
                    >
                        Ver detalles <ArrowRight class="h-3 w-3" />
                    </Link>
                </div>
            </article>
        </div>
    </div>
</template>
