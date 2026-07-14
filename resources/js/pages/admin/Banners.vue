<script setup lang="ts">
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import BannerCarouselTabs from '@/components/admin/banners/BannerCarouselTabs.vue';
import BannerContactEditor from '@/components/admin/banners/BannerContactEditor.vue';
import BannerImageEditor from '@/components/admin/banners/BannerImageEditor.vue';
import BannerSidebar from '@/components/admin/banners/BannerSidebar.vue';
import BannerTextEditor from '@/components/admin/banners/BannerTextEditor.vue';
import { useBannerEditor } from '@/composables/admin/useBannerEditor';
import AppLayout from '@/layouts/AppLayout.vue';
import type { DbBanner } from '@/types/banner';
import { Head } from '@inertiajs/vue3';
import { Loader2, Save } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{ dbBanners: DbBanner[] }>();

const {
    banners,
    activeBannerId,
    activeSlideIndex,
    activeBanner,
    activeSlide,
    errorMessage,
    isSaving,
    fileInput,
    recommendedSpecs,
    triggerUpload,
    handleFileChange,
    saveChanges,
} = useBannerEditor(props.dbBanners);

const carouselPreviews = computed(() => activeBanner.value?.slides.map((s) => s.imagePreview) ?? []);
</script>

<template>
    <Head title="Gestión de Banners - iieEdu Admin" />
    <AppLayout>
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <AdminPageHeader title="Banners del " title-accent="sitio" subtitle="Imágenes de cabecera y textos del home.">
                <button
                    @click="saveChanges"
                    :disabled="isSaving"
                    class="inline-flex h-12 items-center gap-2.5 rounded-2xl bg-primary px-7 text-sm font-bold text-white shadow-xl transition-all duration-300 hover:scale-[1.02] active:scale-95 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <Loader2 v-if="isSaving" class="h-4 w-4 animate-spin" />
                    <Save v-else class="h-4 w-4" />
                    {{ isSaving ? 'Guardando...' : 'Guardar Configuración' }}
                </button>
            </AdminPageHeader>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
                <BannerSidebar v-model:activeBannerId="activeBannerId" :banners="banners" />

                <div class="space-y-6 lg:col-span-3" v-if="activeBanner && activeSlide">
                    <BannerCarouselTabs
                        v-if="activeBanner.isCarousel"
                        v-model:active-index="activeSlideIndex"
                        :slide-count="activeBanner.slides.length"
                        :previews="carouselPreviews"
                    />

                    <input type="file" ref="fileInput" class="hidden" accept="image/png,image/jpeg,image/jpg,image/webp" @change="handleFileChange" />

                    <BannerImageEditor
                        v-model:slide="activeSlide"
                        v-model:errorMessage="errorMessage"
                        :recommended-specs="recommendedSpecs"
                        :is-carousel="activeBanner.isCarousel"
                        :slide-index="activeSlideIndex"
                        @trigger-upload="triggerUpload"
                        @file-change="handleFileChange"
                    />
                    <BannerTextEditor v-model:slide="activeSlide" :is-home="activeBannerId === 'home'" />
                    <BannerContactEditor v-if="activeBannerId === 'consultoria'" v-model:slide="activeSlide" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
