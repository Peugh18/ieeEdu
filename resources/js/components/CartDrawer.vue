<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useCart } from '@/composables/useCart';

const { 
    cartItems, 
    isCartOpen, 
    toggleCart, 
    removeItem, 
    total, 
    isAuthenticated, 
    user, 
    sendToWhatsApp,
    handlePostLoginAction
} = useCart();

onMounted(() => {
    handlePostLoginAction();
});

function getCategoryName(category: any) {
    if (typeof category === 'object' && category !== null) {
        return category.name;
    }
    return category || 'Especialización';
}

function formatDate(dateStr?: string, timeStr?: string) {
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
        <div v-if="isCartOpen" @click="toggleCart" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100]"></div>
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
        <div v-if="isCartOpen" class="fixed top-0 right-0 h-full w-full sm:w-[450px] bg-surface shadow-2xl flex flex-col z-[101] border-l border-outline-variant/20 overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 bg-[url('/images/pattern.svg')] opacity-[0.03] pointer-events-none"></div>

            <!-- Header -->
            <div class="relative px-6 py-5 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-lowest flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-xl" translate="no">shopping_cart</span>
                    </div>
                    <div>
                        <h2 class="font-serif text-xl font-bold text-on-surface">Tu Matrícula</h2>
                        <p class="text-xs text-on-surface-variant uppercase tracking-widest font-bold">{{ cartItems.length }} {{ cartItems.length === 1 ? 'curso' : 'cursos' }} seleccionado(s)</p>
                    </div>
                </div>
                <button @click="toggleCart" class="p-2 rounded-full hover:bg-surface-container-highest transition-colors text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined" translate="no">close</span>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto overflow-x-hidden p-6 space-y-4">
                <template v-if="cartItems.length > 0">
                    <div v-for="item in cartItems" :key="item.id" class="group relative flex gap-4 p-4 rounded-3xl border border-outline-variant/10 bg-white shadow-sm hover:shadow-md hover:border-primary/20 transition-all duration-300">
                        
                        <!-- Mini Image -->
                        <div class="w-[80px] h-[80px] rounded-[1.25rem] bg-surface-container-low overflow-hidden flex-shrink-0 shadow-inner">
                            <img v-if="item.image" :src="item.image" :alt="item.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-outline-variant opacity-50 text-3xl" translate="no">collections</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0 py-1 flex flex-col justify-between">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-primary mb-1 truncate">{{ getCategoryName(item.category) }}</p>
                                <h3 class="font-serif font-bold text-on-surface text-sm leading-tight line-clamp-2" :title="item.title">{{ item.title }}</h3>
                            </div>
                            
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center gap-1.5 opacity-70">
                                    <span class="material-symbols-outlined text-[12px] text-on-surface-variant" translate="no">calendar_month</span>
                                    <span class="text-xs font-medium text-on-surface-variant">{{ formatDate(item.start_date) }}</span>
                                </div>
                                <span class="font-serif font-bold text-lg text-primary">
                                    <span class="text-[10px] uppercase font-sans align-top mt-1 inline-block opacity-60 mr-1.5">S/</span>{{ item.sale_price && item.sale_price > 0 ? item.sale_price : item.price }}
                                </span>
                            </div>
                        </div>

                        <!-- Floating Delete Button -->
                        <button 
                            @click="removeItem(item.id)" 
                            class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-error text-white shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100 transition-all hover:bg-error/90 hover:scale-110 border border-white"
                            title="Remover"
                        >
                            <span class="material-symbols-outlined text-[14px]" translate="no">close</span>
                        </button>
                    </div>
                </template>
                
                <div v-else class="h-full flex flex-col items-center justify-center text-center px-4 py-12">
                    <div class="w-24 h-24 rounded-full bg-surface-container-highest flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-primary opacity-20 text-5xl" translate="no">shopping_bag</span>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-on-surface mb-2">Tu carrito está vacío</h3>
                    <p class="text-on-surface-variant text-sm max-w-[250px] mx-auto leading-relaxed">Descubre nuestros programas de especialización y potencia tu perfil profesional.</p>
                    <button @click="toggleCart" class="mt-8 px-6 py-2.5 rounded-full border-2 border-primary/20 text-primary font-bold text-xs uppercase tracking-widest hover:border-primary hover:bg-primary/5 transition-all">
                        Explorar Catálogo
                    </button>
                </div>
            </div>

            <!-- Footer summary -->
            <div v-if="cartItems.length > 0" class="relative bg-surface-container-lowest p-6 border-t border-outline-variant/10 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] z-10">
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center text-on-surface-variant text-sm">
                        <span>Subtotal</span>
                        <span class="font-serif font-medium">S/ {{ total }}</span>
                    </div>
                    <div class="flex justify-between items-center text-primary">
                        <span class="font-bold uppercase tracking-widest text-xs">Total final</span>
                        <span class="font-serif text-3xl font-bold italic">
                            <span class="text-sm align-top mt-1 inline-block opacity-60 mr-1.5 font-sans not-italic">S/</span>{{ total }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-if="isAuthenticated" class="text-center px-4 py-3 bg-primary/5 rounded-2xl border border-primary/10">
                        <p class="text-xs font-semibold text-primary/80 uppercase tracking-widest">Estás comprando como</p>
                        <p class="text-sm font-bold text-primary mt-0.5">{{ user?.name }}</p>
                    </div>

                    <button 
                        @click="sendToWhatsApp"
                        class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-[#25D366] text-white rounded-[1.5rem] font-bold text-[13px] uppercase tracking-[0.1em] hover:bg-[#20BE5C] shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                    >
                        <span class="material-symbols-outlined text-[20px]" translate="no">chat</span>
                        Reservar por WhatsApp
                    </button>
                    
                    <p class="text-center text-[10px] text-on-surface-variant font-medium mt-3">
                        <span class="material-symbols-outlined text-[10px] inline-block align-middle mr-1" translate="no">lock</span>
                        Pago seguro y encriptado
                    </p>
                </div>
            </div>
        </div>
    </Transition>
</template>
