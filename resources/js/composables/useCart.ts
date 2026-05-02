import { ref, computed } from 'vue';
import { useStorage } from '@vueuse/core';
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

interface Course {
    id: number | string;
    slug: string;
    title: string;
    image?: string;
    category?: { name: string } | string;
    type: string;
    price: number;
    sale_price?: number;
    start_date?: string;
    start_time?: string;
}

const cartItems = useStorage<Course[]>('iee-cart-items', []);
const isCartOpen = ref(false);

export function useCart() {
    const page = usePage();
    const user = computed(() => (page.props as any).auth?.user);
    const isAuthenticated = computed(() => !!user.value);

    const itemCount = computed(() => cartItems.value.length);
    
    const subtotal = computed(() => {
        return cartItems.value.reduce((acc, item) => {
            return acc + (item.sale_price && item.sale_price > 0 ? item.sale_price : item.price);
        }, 0);
    });

    const total = computed(() => subtotal.value);

    function addItem(course: Course) {
        const exists = cartItems.value.find(item => item.id === course.id);
        if (exists) {
            return { success: false, message: 'Este curso ya está en tu carrito' };
        }
        
        cartItems.value.push(course);
        isCartOpen.value = true;
        return { success: true, message: 'Curso agregado ✅' };
    }

    function removeItem(id: number | string) {
        cartItems.value = cartItems.value.filter(item => item.id !== id);
    }

    function toggleCart() {
        isCartOpen.value = !isCartOpen.value;
    }

    function formatDate(dateStr?: string, timeStr?: string) {
        if (!dateStr) return 'Pronto';
        try {
            const date = new Date(dateStr.replace(/-/g, '/'));
            const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long' };
            const dayMonth = new Intl.DateTimeFormat('es-ES', options).format(date);
            
            let timeFormatted = '';
            if (timeStr) {
                const [hours, minutes] = timeStr.split(':');
                let h = parseInt(hours);
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;
                timeFormatted = ` - ${h}:${minutes || '00'} ${ampm}`;
            }
            return `${dayMonth}${timeFormatted}`;
        } catch (e) {
            return dateStr;
        }
    }

    async function sendToWhatsApp() {
        if (cartItems.value.length === 0) return;

        // Validar autenticación
        if (!isAuthenticated.value) {
            localStorage.setItem('redirect_after_login', 'checkout_whatsapp');
            isCartOpen.value = false;
            router.get(route('login'));
            return;
        }

        // Registrar en el backend
        try {
            await axios.post('/api/leads/whatsapp', {
                courses: cartItems.value.map(c => c.id),
                total: total.value
            });
        } catch (e) {
            console.error('Tracking error:', e);
        }

        // Generar mensaje
        const userName = user.value?.name || 'Estudiante';
        const baseUrl = window.location.origin;
        
        // Obtener URL de la imagen (absoluta)
        const firstCourse = cartItems.value[0];
        const imageUrl = firstCourse?.image?.startsWith('http') 
            ? firstCourse.image 
            : `${baseUrl}${firstCourse?.image?.startsWith('/') ? '' : '/'}${firstCourse?.image || 'images/empresa/LogoDark.png'}`;

        const wav = "\u{1F44B}"; // 👋
        const bx = "\u{1F4DA}"; // 📚
        const cal = "\u{1F4C5}"; // 📅
        const mon = "\u{1F4B0}"; // 💰
        const csh = "\u{1F4B5}"; // 💵

        let message = `${imageUrl}\n\n`;
        message += `Hola ${wav}, soy *${userName}* y estoy interesado en los siguientes cursos:\n\n`;
        message += `${bx} *Cursos seleccionados:*\n\n`;
        
        cartItems.value.forEach((item, index) => {
            const price = item.sale_price && item.sale_price > 0 ? item.sale_price : item.price;
            message += `${index + 1}. *${item.title}*\n`;
            message += `   ${cal} Inicia: ${formatDate(item.start_date, item.start_time)}\n`;
            message += `   ${mon} Precio: S/ ${price}\n\n`;
        });

        message += `${csh} *Total a pagar: S/ ${total.value}*\n\n`;
        message += `Quedo atento para continuar con la matrícula.`;
        
        const whatsappNumber = '51959166911'; 
        const url = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
        
        window.open(url, '_blank');
    }

    /**
     * Revisa si hay una acción pendiente después del login
     */
    function handlePostLoginAction() {
        if (typeof window === 'undefined') return;
        const action = localStorage.getItem('redirect_after_login');
        if (action === 'checkout_whatsapp' && isAuthenticated.value && cartItems.value.length > 0) {
            localStorage.removeItem('redirect_after_login');
            setTimeout(() => {
                isCartOpen.value = true;
                sendToWhatsApp();
            }, 500);
        }
    }

    return {
        cartItems,
        isCartOpen,
        user,
        isAuthenticated,
        itemCount,
        subtotal,
        total,
        addItem,
        removeItem,
        toggleCart,
        sendToWhatsApp,
        handlePostLoginAction
    };
}
