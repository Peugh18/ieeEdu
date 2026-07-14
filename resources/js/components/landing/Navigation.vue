<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import BrandLogo from '@/components/BrandLogo.vue';
import CartDrawer from '@/components/CartDrawer.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { DropdownMenu, DropdownMenuContent } from '@/components/ui/dropdown-menu';
import { useCart } from '@/composables/useCart';
import { ChevronsUpDown } from 'lucide-vue-next';
import { DropdownMenuTrigger } from 'radix-vue';

const { itemCount, toggleCart } = useCart();

const page = usePage();
const isAuthenticated = computed(() => (page.props as any).auth?.user);
const user = computed(() => (page.props as any).auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const avatarUrl = computed(() =>
    user.value?.avatar
        ? '/storage/' + user.value.avatar
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value?.name ?? '')}&background=57572A&color=e7e6ab&bold=true&size=128`,
);
const mobileMenuOpen = ref(false);
const scrolled = ref(false);

// Track scroll for navbar styling
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 10;
    });
}

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const menuItems = [
    { label: 'Nosotros', href: route('nosotros') },
    { label: 'Cursos', href: route('cursos.index') },
    { label: 'Consultoría', href: route('consultoria') },
    { label: 'Publicaciones', href: route('publicaciones.index') },
    { label: 'Masterclass', href: route('masterclass.index') },
    { label: 'Premium', href: route('planes'), isPremium: true },
];

const isActive = (href: string) => {
    if (!href) return false;
    try {
        const path = new URL(href).pathname;
        if (path === '/') return page.url === '/';
        return page.url.startsWith(path);
    } catch {
        return false;
    }
};
</script>

<template>
    <!-- Navbar -->
    <nav
        :class="[
            'fixed top-0 z-50 w-full transition-all duration-300',
            scrolled
                ? 'border-b border-outline-variant/20 bg-surface/95 shadow-lg backdrop-blur-2xl'
                : 'border-b border-outline-variant/5 bg-surface/70 backdrop-blur-xl',
        ]"
    >
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-4 md:px-8">
            <!-- Logo -->
            <Link href="/" class="flex items-center gap-2 transition-opacity hover:opacity-80">
                <BrandLogo imageClass="h-14 sm:h-16 w-auto object-contain" />
            </Link>

            <!-- Desktop Menu -->
            <div class="hidden items-center gap-2 md:gap-8 lg:flex">
                <Link
                    v-for="item in menuItems"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        item.isPremium
                            ? 'flex transform items-center gap-1.5 rounded-full bg-gradient-to-r from-[#D4AF37] to-[#AA7C11] px-5 py-2 text-xs font-black uppercase tracking-widest text-white shadow-[0_4px_15px_rgba(212,175,55,0.4)] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_6px_25px_rgba(212,175,55,0.6)]'
                            : isActive(item.href)
                              ? 'group relative text-sm font-bold text-primary transition-colors duration-300'
                              : 'group relative text-sm font-medium text-on-surface-variant transition-colors duration-300 hover:text-primary',
                    ]"
                >
                    <span v-if="item.isPremium" class="material-symbols-outlined" translate="no" style="font-size: 16px">hotel_class</span>
                    {{ item.label }}
                    <span
                        v-if="!item.isPremium"
                        :class="[
                            'absolute bottom-0 left-0 h-0.5 bg-primary transition-all duration-300',
                            isActive(item.href) ? 'w-full' : 'w-0 group-hover:w-full'
                        ]"
                    ></span>
                </Link>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-4">
                <!-- Cart Button -->
                <button @click="toggleCart" class="group relative rounded-xl p-2.5 text-primary transition-all duration-300 hover:bg-primary/5">
                    <span class="material-symbols-outlined text-2xl transition-transform group-hover:scale-110" translate="no">shopping_cart</span>
                    <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="scale-0" enter-to-class="scale-100">
                        <span
                            v-if="itemCount > 0"
                            class="absolute -right-0.5 -top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-[#D32F2F] text-[10px] font-black text-white shadow-lg ring-2 ring-surface"
                        >
                            {{ itemCount }}
                        </span>
                    </Transition>
                </button>

                <!-- Auth Buttons Desktop -->
                <div class="hidden items-center gap-3 sm:flex">
                    <template v-if="!isAuthenticated">
                        <Link
                            :href="route('login')"
                            class="rounded-lg border-2 border-primary/40 px-5 py-2 text-xs font-bold uppercase tracking-widest text-primary transition-all duration-300 hover:border-primary hover:bg-primary/5"
                        >
                            Ingresar
                        </Link>
                        <Link
                            :href="route('register')"
                            class="transform rounded-lg bg-primary px-5 py-2 text-sm font-bold text-on-primary shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:opacity-90 hover:shadow-xl"
                        >
                            Registrarse
                        </Link>
                    </template>

                    <!-- User Menu Dropdown (Authed User Widget like Sidebar Card) -->
                    <template v-else>
                        <Link
                            :href="route('dashboard')"
                            class="transform rounded-lg bg-primary px-5 py-2 text-sm font-bold text-on-primary shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:opacity-90 hover:shadow-xl"
                        >
                            Dashboard
                        </Link>

                        <div class="relative">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <button
                                        type="button"
                                        class="group flex items-center gap-2 rounded-xl !border-0 !bg-transparent px-2 py-1.5 text-left !shadow-none outline-none transition-all duration-300 hover:!bg-on-surface-variant/5 dark:hover:!bg-white/5"
                                        aria-label="Abrir menú de cuenta"
                                    >
                                        <div class="relative flex-shrink-0">
                                            <img
                                                :src="avatarUrl"
                                                :alt="user.name"
                                                class="h-8 w-8 rounded-full border border-outline-variant/10 object-cover shadow-sm transition-transform duration-300 group-hover:scale-105 dark:border-slate-700/30"
                                            />
                                            <div
                                                class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-[1.5px] border-surface bg-emerald-400"
                                            ></div>
                                        </div>

                                        <div class="flex max-w-[120px] flex-col justify-center overflow-hidden sm:max-w-[150px]">
                                            <span class="truncate text-xs font-bold leading-tight tracking-wide text-on-surface">
                                                {{ user.name }}
                                            </span>
                                            <span
                                                class="truncate text-[10px] font-medium text-on-surface-variant/65 transition-colors group-hover:text-primary dark:text-slate-400 dark:group-hover:text-primary"
                                            >
                                                {{ isAdmin ? 'Admin · Mi cuenta' : 'Estudiante' }}
                                            </span>
                                        </div>

                                        <ChevronsUpDown
                                            class="h-3.5 w-3.5 shrink-0 text-on-surface-variant/40 transition-colors group-hover:text-on-surface-variant"
                                        />
                                    </button>
                                </DropdownMenuTrigger>

                                <DropdownMenuContent
                                    class="z-[100] w-56 rounded-xl border border-outline-variant/15 bg-surface-container-lowest p-1 shadow-xl"
                                    side="bottom"
                                    align="end"
                                    :side-offset="6"
                                >
                                    <UserMenuContent :user="user" :is-admin="isAdmin" />
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="toggleMobileMenu" class="rounded-lg p-2 transition-colors hover:bg-outline-variant/10 lg:hidden">
                    <svg v-if="!mobileMenuOpen" class="h-6 w-6 text-on-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="h-6 w-6 text-on-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
        >
            <div v-if="mobileMenuOpen" class="bg-surface/98 border-b border-outline-variant/10 backdrop-blur-lg lg:hidden">
                <div class="mx-auto max-w-7xl space-y-3 px-6 py-4">
                    <Link
                        v-for="item in menuItems"
                        :key="item.href"
                        :href="item.href"
                        @click="mobileMenuOpen = false"
                        :class="[
                            'block rounded-lg px-3 py-2 font-medium transition-all',
                            item.isPremium
                                ? 'mt-2 bg-gradient-to-r from-[#D4AF37] to-[#AA7C11] text-center font-bold uppercase tracking-widest text-white shadow-md'
                                : isActive(item.href)
                                  ? 'bg-primary/10 text-primary font-bold'
                                  : 'text-on-surface-variant hover:bg-primary/5 hover:text-primary',
                        ]"
                    >
                        <span
                            v-if="item.isPremium"
                            class="material-symbols-outlined mr-1"
                            translate="no"
                            style="font-size: 18px; vertical-align: text-bottom"
                            >hotel_class</span
                        >
                        {{ item.label }}
                    </Link>
                    <div class="flex gap-2 border-t border-outline-variant/10 pt-2">
                        <Link
                            v-if="!isAuthenticated"
                            :href="route('login')"
                            class="flex-1 rounded-lg border border-primary/40 px-4 py-2 text-center text-xs font-bold uppercase text-primary transition-all hover:bg-primary/5"
                        >
                            Ingresar
                        </Link>
                        <Link
                            :href="isAuthenticated ? route('dashboard') : route('register')"
                            class="flex-1 rounded-lg bg-primary px-4 py-2 text-center text-xs font-bold text-on-primary transition-opacity hover:opacity-90"
                        >
                            {{ isAuthenticated ? 'Dashboard' : 'Registrarse' }}
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>
    </nav>

    <CartDrawer />
</template>
