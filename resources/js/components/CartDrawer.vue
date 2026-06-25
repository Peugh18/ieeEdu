<script setup lang="ts">
import { useCart } from '@/composables/useCart';
import { onMounted } from 'vue';

const { cartItems, isCartOpen, toggleCart, removeItem, total, isAuthenticated, user, sendToWhatsApp, handlePostLoginAction } = useCart();

onMounted(() => {
    handlePostLoginAction();
});

function getCategoryName(category: string | { name: string } | null) {
    if (!category) return 'Sin categoría';
    return typeof category === 'string' ? category : category.name;
}

function formatDate(dateStr?: string, _timeStr?: string) {
    if (!dateStr) return 'Pronto';
    try {
        const date = new Date(dateStr.replace(/-/g, '/'));
        const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long' };
        const dayMonth = new Intl.DateTimeFormat('es-ES', options).format(date);
        return dayMonth;
    } catch {
        return dateStr;
    }
}
</script>

<template>
    <!-- Overlay -->
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="isCartOpen" @click="toggleCart" class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm"></div>
    </Transition>

    <!-- Drawer -->
    <Transition
        enter-active-class="transition duration-400 cubic-bezier(0.4, 0, 0.2, 1)"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition duration-300 cubic-bezier(0.4, 0, 0.2, 1)"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div
            v-if="isCartOpen"
            class="fixed right-0 top-0 z-[101] flex h-full w-full flex-col overflow-hidden border-l border-outline-variant/20 bg-surface shadow-2xl sm:w-[450px]"
        >
            <!-- Background pattern -->
            <div class="pointer-events-none absolute inset-0 bg-[url('/images/pattern.svg')] opacity-[0.03]"></div>

            <!-- Header -->
            <div
                class="relative flex flex-shrink-0 items-center justify-between border-b border-outline-variant/10 bg-surface-container-lowest px-6 py-5"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                        <span class="material-symbols-outlined text-xl text-primary" translate="no">shopping_cart</span>
                    </div>
                    <div>
                        <h2 class="font-serif text-xl font-bold text-on-surface">Tu Matrícula</h2>
                        <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                            {{ cartItems.length }} {{ cartItems.length === 1 ? 'curso' : 'cursos' }} seleccionado(s)
                        </p>
                    </div>
                </div>
                <button
                    @click="toggleCart"
                    class="rounded-full p-2 text-on-surface-variant transition-colors hover:bg-surface-container-highest hover:text-on-surface"
                >
                    <span class="material-symbols-outlined" translate="no">close</span>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 space-y-4 overflow-y-auto overflow-x-hidden p-6">
                <template v-if="cartItems.length > 0">
                    <div
                        v-for="item in cartItems"
                        :key="item.id"
                        class="group relative flex gap-4 rounded-3xl border border-outline-variant/10 bg-white p-4 shadow-sm transition-all duration-300 hover:border-primary/20 hover:shadow-md"
                    >
                        <!-- Mini Image -->
                        <div class="h-[80px] w-[80px] flex-shrink-0 overflow-hidden rounded-[1.25rem] bg-surface-container-low shadow-inner">
                            <img
                                v-if="item.image"
                                :src="item.image"
                                :alt="item.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center">
                                <span class="material-symbols-outlined text-3xl text-outline-variant opacity-50" translate="no">collections</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex min-w-0 flex-1 flex-col justify-between py-1">
                            <div>
                                <p class="mb-1 truncate text-[9px] font-black uppercase tracking-[0.2em] text-primary">
                                    {{ getCategoryName(item.category) }}
                                </p>
                                <h3 class="line-clamp-2 font-serif text-sm font-bold leading-tight text-on-surface" :title="item.title">
                                    {{ item.title }}
                                </h3>
                            </div>

                            <div class="mt-2 flex items-center justify-between">
                                <div class="flex items-center gap-1.5 opacity-70">
                                    <span class="material-symbols-outlined text-[12px] text-on-surface-variant" translate="no">calendar_month</span>
                                    <span class="text-xs font-medium text-on-surface-variant">{{ formatDate(item.start_date) }}</span>
                                </div>
                                <span class="font-serif text-lg font-bold text-primary">
                                    <span class="mr-1.5 mt-1 inline-block align-top font-sans text-[10px] uppercase opacity-60">S/</span
                                    >{{ item.sale_price && item.sale_price > 0 ? item.sale_price : item.price }}
                                </span>
                            </div>
                        </div>

                        <!-- Floating Delete Button -->
                        <button
                            @click="removeItem(item.id)"
                            class="absolute -right-2 -top-2 flex h-7 w-7 scale-75 items-center justify-center rounded-full border border-white bg-error text-white opacity-0 shadow-lg transition-all hover:scale-110 hover:bg-error/90 group-hover:scale-100 group-hover:opacity-100"
                            title="Remover"
                        >
                            <span class="material-symbols-outlined text-[14px]" translate="no">close</span>
                        </button>
                    </div>
                </template>

                <div v-else class="flex h-full flex-col items-center justify-center px-4 py-12 text-center">
                    <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-surface-container-highest">
                        <span class="material-symbols-outlined text-5xl text-primary opacity-20" translate="no">shopping_bag</span>
                    </div>
                    <h3 class="mb-2 font-serif text-xl font-bold text-on-surface">Tu carrito está vacío</h3>
                    <p class="mx-auto max-w-[250px] text-sm leading-relaxed text-on-surface-variant">
                        Descubre nuestros programas de especialización y potencia tu perfil profesional.
                    </p>
                    <button
                        @click="toggleCart"
                        class="mt-8 rounded-full border-2 border-primary/20 px-6 py-2.5 text-xs font-bold uppercase tracking-widest text-primary transition-all hover:border-primary hover:bg-primary/5"
                    >
                        Explorar Catálogo
                    </button>
                </div>
            </div>

            <!-- Footer summary -->
            <div
                v-if="cartItems.length > 0"
                class="relative z-10 border-t border-outline-variant/10 bg-surface-container-lowest p-6 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]"
            >
                <div class="mb-6 space-y-4">
                    <div class="flex items-center justify-between text-sm text-on-surface-variant">
                        <span>Subtotal</span>
                        <span class="font-serif font-medium">S/ {{ total }}</span>
                    </div>
                    <div class="flex items-center justify-between text-primary">
                        <span class="text-xs font-bold uppercase tracking-widest">Total final</span>
                        <span class="font-serif text-3xl font-bold italic">
                            <span class="mr-1.5 mt-1 inline-block align-top font-sans text-sm not-italic opacity-60">S/</span>{{ total }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-if="isAuthenticated" class="rounded-2xl border border-primary/10 bg-primary/5 px-4 py-3 text-center">
                        <p class="text-xs font-semibold uppercase tracking-widest text-primary/80">Estás comprando como</p>
                        <p class="mt-0.5 text-sm font-bold text-primary">{{ user?.name }}</p>
                    </div>

                    <button
                        @click="sendToWhatsApp"
                        class="flex w-full items-center justify-center gap-3 rounded-[1.5rem] bg-[#25D366] px-6 py-4 text-[13px] font-bold uppercase tracking-[0.1em] text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#20BE5C] hover:shadow-xl"
                    >
                        <span class="material-symbols-outlined text-[20px]" translate="no">chat</span>
                        Reservar por WhatsApp
                    </button>

                    <p class="mt-3 text-center text-[10px] font-medium text-on-surface-variant">
                        <span class="material-symbols-outlined mr-1 inline-block align-middle text-[10px]" translate="no">lock</span>
                        Pago seguro y encriptado
                    </p>
                </div>
            </div>
        </div>
    </Transition>
</template>
