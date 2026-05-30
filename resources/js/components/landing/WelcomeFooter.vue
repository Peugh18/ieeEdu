<script setup lang="ts">
import BrandLogo from '@/components/BrandLogo.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SharedData } from '@/types';

const page = usePage<SharedData>();
const site = computed(() => (page.props.site_settings as {
    social_facebook?: string;
    social_linkedin?: string;
    contact_email?: string;
    contact_address?: string;
}) ?? {});

const facebookUrl = computed(() => site.value.social_facebook || null);
const linkedinUrl = computed(() => site.value.social_linkedin || null);
const contactEmail = computed(() => site.value.contact_email || 'info@iee.edu.pe');
const contactAddress = computed(() => site.value.contact_address || 'Trujillo, Perú');
</script>

<template>
  <footer class="bg-surface-container relative">
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-outline-variant/30 to-transparent"></div>
    <div class="max-w-7xl mx-auto px-6 md:px-8 pt-12 pb-8 md:pt-16 md:pb-12">
      <div class="md:hidden space-y-8">
        <div class="text-center">
          <BrandLogo imageClass="h-12 w-auto object-contain mx-auto mb-4" />
          <p class="text-on-surface-variant text-sm leading-relaxed max-w-xs mx-auto">
            Formamos líderes con visión global desde {{ contactAddress }}.
          </p>
        </div>
        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-center">
          <a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Cursos</a>
          <a href="/masterclass" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Masterclasses</a>
          <a href="/consultoria" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Consultoría</a>
          <a href="/publicaciones" class="text-on-surface-variant text-sm hover:text-primary transition-colors py-2">Biblioteca</a>
        </div>
        <div class="text-center space-y-4">
          <div class="flex justify-center gap-4">
            <a v-if="facebookUrl" :href="facebookUrl" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a v-if="linkedinUrl" :href="linkedinUrl" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
          </div>
          <p class="text-on-surface-variant/70 text-sm">{{ contactEmail }}</p>
        </div>
        <div class="text-center">
          <a href="/login" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-on-primary rounded-xl text-sm font-bold hover:shadow-md transition-all">
            Acceder al Aula
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>

      <div class="hidden md:grid grid-cols-4 gap-8 lg:gap-12">
        <div class="col-span-1">
          <BrandLogo imageClass="h-12 w-auto object-contain mb-4" />
          <p class="text-on-surface-variant text-sm leading-relaxed mb-4">
            Formamos líderes desde {{ contactAddress }}. Más de 15 años de trayectoria.
          </p>
          <div class="flex gap-2">
            <a v-if="facebookUrl" :href="facebookUrl" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a v-if="linkedinUrl" :href="linkedinUrl" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-surface-container-high border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-all">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
          </div>
        </div>

        <div>
          <h4 class="font-bold text-on-surface mb-4 text-sm uppercase tracking-wider">Cursos</h4>
          <ul class="space-y-2">
            <li><a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors">En Vivo</a></li>
            <li><a href="/cursos" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Grabados</a></li>
            <li><a href="/masterclass" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Masterclasses</a></li>
            <li><a href="/planes" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Planes</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-bold text-on-surface mb-4 text-sm uppercase tracking-wider">Servicios</h4>
          <ul class="space-y-2">
            <li><a href="/consultoria" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Consultoría</a></li>
            <li><a href="/publicaciones" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Biblioteca</a></li>
            <li><a href="/login" class="text-on-surface-variant text-sm hover:text-primary transition-colors">Aula Virtual</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-bold text-on-surface mb-4 text-sm uppercase tracking-wider">Contacto</h4>
          <ul class="space-y-2 text-on-surface-variant text-sm">
            <li>{{ contactAddress }}</li>
            <li>{{ contactEmail }}</li>
          </ul>
          <a href="/login" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-primary text-on-primary rounded-lg text-xs font-bold hover:shadow-md transition-all">
            Acceder
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>

      <div class="mt-10 pt-6 border-t border-outline-variant/10 text-center">
        <p class="text-on-surface-variant/50 text-xs">
          © {{ new Date().getFullYear() }} Instituto de Economía y Empresa. Todos los derechos reservados.
        </p>
        <div class="flex justify-center gap-4 mt-3 text-xs text-on-surface-variant/50">
          <a href="#" class="hover:text-primary transition-colors">Privacidad</a>
          <a href="#" class="hover:text-primary transition-colors">Términos</a>
          <a href="#" class="hover:text-primary transition-colors">Cookies</a>
        </div>
      </div>
    </div>
  </footer>
</template>
