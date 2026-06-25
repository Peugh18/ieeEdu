<script setup lang="ts">
import BrandLogo from '@/components/BrandLogo.vue';
import type { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<SharedData>();
const site = computed(
    () =>
        (page.props.site_settings as {
            social_facebook?: string;
            social_linkedin?: string;
            social_instagram?: string;
            social_youtube?: string;
            social_tiktok?: string;
            social_twitter?: string;
            contact_email?: string;
            contact_address?: string;
        }) ?? {},
);

const facebookUrl = computed(() => site.value.social_facebook || null);
const linkedinUrl = computed(() => site.value.social_linkedin || null);
const instagramUrl = computed(() => site.value.social_instagram || null);
const youtubeUrl = computed(() => site.value.social_youtube || null);
const tiktokUrl = computed(() => site.value.social_tiktok || null);
const twitterUrl = computed(() => site.value.social_twitter || null);
const contactEmail = computed(() => site.value.contact_email || 'info@iee.edu.pe');
const contactAddress = computed(() => site.value.contact_address || 'Trujillo, Perú');
</script>

<template>
    <footer class="relative bg-surface-container">
        <div class="absolute left-0 right-0 top-0 h-px bg-gradient-to-r from-transparent via-outline-variant/30 to-transparent"></div>
        <div class="mx-auto max-w-7xl px-6 pb-8 pt-12 md:px-8 md:pb-12 md:pt-16">
            <div class="space-y-8 md:hidden">
                <div class="text-center">
                    <BrandLogo imageClass="h-12 w-auto object-contain mx-auto mb-4" />
                    <p class="mx-auto max-w-xs text-sm leading-relaxed text-on-surface-variant">
                        Formamos líderes con visión global desde {{ contactAddress }}.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-center">
                    <a href="/cursos" class="py-2 text-sm text-on-surface-variant transition-colors hover:text-primary">Cursos</a>
                    <a href="/masterclass" class="py-2 text-sm text-on-surface-variant transition-colors hover:text-primary">Masterclasses</a>
                    <a href="/consultoria" class="py-2 text-sm text-on-surface-variant transition-colors hover:text-primary">Consultoría</a>
                    <a href="/publicaciones" class="py-2 text-sm text-on-surface-variant transition-colors hover:text-primary">Biblioteca</a>
                </div>
                <div class="space-y-4 text-center">
                    <div class="flex flex-wrap justify-center gap-4">
                        <a
                            v-if="facebookUrl"
                            :href="facebookUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="Facebook"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="instagramUrl"
                            :href="instagramUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="Instagram"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="linkedinUrl"
                            :href="linkedinUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="LinkedIn"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="youtubeUrl"
                            :href="youtubeUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="YouTube"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.516 0-9.387.507a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.871.507 9.387.507 9.387.507s7.517 0 9.387-.507a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="tiktokUrl"
                            :href="tiktokUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="TikTok"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.58-.98-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.92-1.88 2.63-5.31 3.49-8.15 2.23-2.73-1.2-4.38-4.13-4.06-7.11.24-2.27 1.56-4.3 3.55-5.15 1.6-.7 3.41-.66 4.96.11.02 1.48-.01 2.96-.01 4.44-1.07-.63-2.42-.71-3.52-.09-1.2.66-1.85 2.05-1.63 3.4.19 1.14 1.05 2.11 2.18 2.37 1.43.34 2.99-.44 3.47-1.8.12-.34.16-.71.16-1.07-.01-5.39-.01-10.79-.01-16.18z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="twitterUrl"
                            :href="twitterUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="Twitter / X"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"
                                />
                            </svg>
                        </a>
                    </div>
                    <p class="text-sm text-on-surface-variant/70">{{ contactEmail }}</p>
                </div>
                <div class="text-center">
                    <a
                        href="/login"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-bold text-on-primary transition-all hover:shadow-md"
                    >
                        Acceder al Aula
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="hidden grid-cols-4 gap-8 md:grid lg:gap-12">
                <div class="col-span-1">
                    <BrandLogo imageClass="h-12 w-auto object-contain mb-4" />
                    <p class="mb-4 text-sm leading-relaxed text-on-surface-variant">
                        Formamos líderes desde {{ contactAddress }}. Más de 15 años de trayectoria.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <a
                            v-if="facebookUrl"
                            :href="facebookUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="Facebook"
                        >
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="instagramUrl"
                            :href="instagramUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="Instagram"
                        >
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="linkedinUrl"
                            :href="linkedinUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="LinkedIn"
                        >
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="youtubeUrl"
                            :href="youtubeUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="YouTube"
                        >
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.517 3.545 12 3.545 12 3.545s-7.516 0-9.387.507a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.871.507 9.387.507 9.387.507s7.517 0 9.387-.507a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="tiktokUrl"
                            :href="tiktokUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="TikTok"
                        >
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.58-.98-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.92-1.88 2.63-5.31 3.49-8.15 2.23-2.73-1.2-4.38-4.13-4.06-7.11.24-2.27 1.56-4.3 3.55-5.15 1.6-.7 3.41-.66 4.96.11.02 1.48-.01 2.96-.01 4.44-1.07-.63-2.42-.71-3.52-.09-1.2.66-1.85 2.05-1.63 3.4.19 1.14 1.05 2.11 2.18 2.37 1.43.34 2.99-.44 3.47-1.8.12-.34.16-.71.16-1.07-.01-5.39-.01-10.79-.01-16.18z"
                                />
                            </svg>
                        </a>
                        <a
                            v-if="twitterUrl"
                            :href="twitterUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-outline-variant/20 bg-surface-container-high text-on-surface-variant transition-all hover:border-primary/30 hover:text-primary"
                            title="Twitter / X"
                        >
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-on-surface">Cursos</h4>
                    <ul class="space-y-2">
                        <li><a href="/cursos" class="text-sm text-on-surface-variant transition-colors hover:text-primary">En Vivo</a></li>
                        <li><a href="/cursos" class="text-sm text-on-surface-variant transition-colors hover:text-primary">Grabados</a></li>
                        <li><a href="/masterclass" class="text-sm text-on-surface-variant transition-colors hover:text-primary">Masterclasses</a></li>
                        <li><a href="/planes" class="text-sm text-on-surface-variant transition-colors hover:text-primary">Planes</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-on-surface">Servicios</h4>
                    <ul class="space-y-2">
                        <li><a href="/consultoria" class="text-sm text-on-surface-variant transition-colors hover:text-primary">Consultoría</a></li>
                        <li><a href="/publicaciones" class="text-sm text-on-surface-variant transition-colors hover:text-primary">Biblioteca</a></li>
                        <li><a href="/login" class="text-sm text-on-surface-variant transition-colors hover:text-primary">Aula Virtual</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-on-surface">Contacto</h4>
                    <ul class="space-y-2 text-sm text-on-surface-variant">
                        <li>{{ contactAddress }}</li>
                        <li>{{ contactEmail }}</li>
                    </ul>
                    <a
                        href="/login"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-on-primary transition-all hover:shadow-md"
                    >
                        Acceder
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="mt-10 border-t border-outline-variant/10 pt-6 text-center">
                <p class="text-xs text-on-surface-variant/50">
                    © {{ new Date().getFullYear() }} Instituto de Economía y Empresa. Todos los derechos reservados.
                </p>
                <div class="mt-3 flex justify-center gap-4 text-xs text-on-surface-variant/50">
                    <Link :href="route('privacy')" class="transition-colors hover:text-primary">Privacidad</Link>
                    <Link :href="route('terms')" class="transition-colors hover:text-primary">Términos</Link>
                    <a href="#" class="transition-colors hover:text-primary">Cookies</a>
                </div>
            </div>
        </div>
    </footer>
</template>
