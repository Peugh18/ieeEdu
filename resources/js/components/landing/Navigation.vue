<script setup lang="ts">
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const page = usePage();
const isAuthenticated = computed(() => page.props.auth.user);
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
    { label: 'Cursos', href: '#cursos' },
    { label: 'Consultoría', href: '#consultoria' },
    { label: 'Publicaciones', href: '#publicaciones' },
    { label: 'Sobre Nosotros', href: '#nosotros' },
];
</script>

<template>
    <!-- Navbar -->
    <nav :class="[
        'fixed top-0 w-full z-50 transition-all duration-300',
        scrolled 
            ? 'bg-surface/95 shadow-lg backdrop-blur-2xl border-b border-outline-variant/20' 
            : 'bg-surface/70 backdrop-blur-xl border-b border-outline-variant/5'
    ]">
        <div class="flex justify-between items-center w-full px-6 md:px-8 py-4 max-w-7xl mx-auto">
            <!-- Logo -->
            <Link href="/" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <img src="/images/empresa/IEE-Logo02.png" alt="IEE Instituto" class="h-14 sm:h-16 w-auto object-contain" />
            </Link>
            
            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-2 md:gap-8">
                <a 
                    v-for="item in menuItems" 
                    :key="item.href"
                    :href="item.href" 
                    class="text-on-surface-variant hover:text-primary font-medium text-sm transition-colors duration-300 relative group"
                >
                    {{ item.label }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary group-hover:w-full transition-all duration-300"></span>
                </a>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-4">
                <!-- Auth Buttons Desktop -->
                <div class="hidden sm:flex items-center gap-3">
                    <Link 
                        v-if="!isAuthenticated" 
                        :href="route('login')"
                        class="border-2 border-primary/40 text-primary px-5 py-2 rounded-lg text-xs font-bold tracking-widest uppercase hover:border-primary hover:bg-primary/5 transition-all duration-300"
                    >
                        Ingresar
                    </Link>
                    <Link 
                        :href="isAuthenticated ? route('dashboard') : route('register')"
                        class="bg-primary text-on-primary px-5 py-2 rounded-lg text-sm font-bold shadow-lg hover:shadow-xl hover:opacity-90 transition-all duration-300 transform hover:-translate-y-0.5"
                    >
                        {{ isAuthenticated ? 'Dashboard' : 'Registrarse' }}
                    </Link>
                </div>

                <!-- Mobile Menu Button -->
                <button 
                    @click="toggleMobileMenu"
                    class="lg:hidden p-2 hover:bg-outline-variant/10 rounded-lg transition-colors"
                >
                    <svg v-if="!mobileMenuOpen" class="w-6 h-6 text-on-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-6 h-6 text-on-surface" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div v-if="mobileMenuOpen" class="lg:hidden bg-surface/98 backdrop-blur-lg border-b border-outline-variant/10">
                <div class="px-6 py-4 space-y-3 max-w-7xl mx-auto">
                    <a 
                        v-for="item in menuItems" 
                        :key="item.href"
                        :href="item.href"
                        @click="mobileMenuOpen = false"
                        class="block px-3 py-2 text-on-surface-variant hover:text-primary hover:bg-primary/5 rounded-lg transition-colors font-medium"
                    >
                        {{ item.label }}
                    </a>
                    <div class="flex gap-2 pt-2 border-t border-outline-variant/10">
                        <Link 
                            v-if="!isAuthenticated" 
                            :href="route('login')"
                            class="flex-1 border border-primary/40 text-primary px-4 py-2 rounded-lg text-xs font-bold uppercase hover:bg-primary/5 transition-all text-center"
                        >
                            Ingresar
                        </Link>
                        <Link 
                            :href="isAuthenticated ? route('dashboard') : route('register')"
                            class="flex-1 bg-primary text-on-primary px-4 py-2 rounded-lg text-xs font-bold text-center hover:opacity-90 transition-opacity"
                        >
                            {{ isAuthenticated ? 'Dashboard' : 'Registrarse' }}
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>
    </nav>
</template>
