<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Save, Loader2 } from 'lucide-vue-next';
import { computed } from 'vue';
import { useBannerEditor } from '@/composables/admin/useBannerEditor';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import BannerCarouselTabs from '@/components/admin/banners/BannerCarouselTabs.vue';
import BannerSidebar from '@/components/admin/banners/BannerSidebar.vue';
import BannerImageEditor from '@/components/admin/banners/BannerImageEditor.vue';
import BannerTextEditor from '@/components/admin/banners/BannerTextEditor.vue';
import BannerContactEditor from '@/components/admin/banners/BannerContactEditor.vue';
import type { DbBanner } from '@/types/banner';

const props = defineProps<{ dbBanners: DbBanner[] }>();

const {
    banners, activeBannerId, activeSlideIndex, activeBanner, activeSlide,
    errorMessage, isSaving, fileInput, recommendedSpecs,
    triggerUpload, handleFileChange, saveChanges,
} = useBannerEditor(props.dbBanners);

const carouselPreviews = computed(() =>
    activeBanner.value?.slides.map((s) => s.imagePreview) ?? [],
);
</script>

<template>
    <Head title="Gestión de Banners - iieEdu Admin" />
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-8">
            <AdminPageHeader
                title="Gestión de"
                title-accent="Banners"
                subtitle="Configura las imágenes de cabecera sincronizadas con la base de datos."
                badge="Marketing / Web"
            >
                <button @click="saveChanges" :disabled="isSaving" class="h-12 inline-flex items-center gap-2.5 rounded-2xl px-7 text-sm font-bold shadow-xl hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed bg-primary text-white">
                    <Loader2 v-if="isSaving" class="h-4 w-4 animate-spin" />
                    <Save v-else class="h-4 w-4" />
                    {{ isSaving ? 'Guardando...' : 'Guardar Configuración' }}
                </button>
            </AdminPageHeader>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <BannerSidebar v-model:activeBannerId="activeBannerId" :banners="banners" />

                <div class="lg:col-span-3 space-y-6" v-if="activeBanner && activeSlide">
                    <BannerCarouselTabs
                        v-if="activeBanner.isCarousel"
                        v-model:active-index="activeSlideIndex"
                        :slide-count="activeBanner.slides.length"
                        :previews="carouselPreviews"
                    />

                    <input type="file" ref="fileInput" class="hidden" accept="image/png,image/jpeg,image/jpg,image/webp" @change="handleFileChange" />

                    <BannerImageEditor v-model:slide="activeSlide" v-model:errorMessage="errorMessage" :recommended-specs="recommendedSpecs" :is-carousel="activeBanner.isCarousel" :slide-index="activeSlideIndex" @trigger-upload="triggerUpload" @file-change="handleFileChange" />
                    <BannerTextEditor v-model:slide="activeSlide" :is-home="activeBannerId === 'home'" />
                    <BannerContactEditor v-if="activeBannerId === 'consultoria'" v-model:slide="activeSlide" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-spin { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
