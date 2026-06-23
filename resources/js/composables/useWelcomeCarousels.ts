import { onMounted, onUnmounted, ref, watch, type Ref } from 'vue';

export function useWelcomeCarousels<T>(items: Ref<T[]>, filterKey: Ref<string>, filterFn: (item: T, filter: string) => boolean) {
    const currentIndex = ref(0);
    const isMobile = ref(false);
    const scrollContainer = ref<HTMLElement | null>(null);
    let autoplayInterval: number | null = null;

    const filteredItems = ref<T[]>([]);

    const checkMobile = () => {
        isMobile.value = window.innerWidth < 768;
    };

    const startAutoplay = () => {
        if (autoplayInterval) clearInterval(autoplayInterval);
        autoplayInterval = window.setInterval(() => {
            if (filteredItems.value.length > 1) {
                currentIndex.value = (currentIndex.value + 1) % filteredItems.value.length;
            }
            if (scrollContainer.value && !isMobile.value) {
                const container = scrollContainer.value;
                if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 10) {
                    container.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    const amount = container.clientWidth * 0.8;
                    container.scrollBy({ left: amount, behavior: 'smooth' });
                }
            }
        }, 4000);
    };

    const stopAutoplay = () => {
        if (autoplayInterval) {
            clearInterval(autoplayInterval);
            autoplayInterval = null;
        }
    };

    const goTo = (index: number) => {
        currentIndex.value = index;
        startAutoplay();
    };

    const next = () => {
        if (filteredItems.value.length > 0) {
            currentIndex.value = (currentIndex.value + 1) % filteredItems.value.length;
            startAutoplay();
        }
    };

    const prev = () => {
        if (filteredItems.value.length > 0) {
            currentIndex.value = (currentIndex.value - 1 + filteredItems.value.length) % filteredItems.value.length;
            startAutoplay();
        }
    };

    const scroll = (direction: 'left' | 'right') => {
        if (scrollContainer.value) {
            const amount = scrollContainer.value.clientWidth * 0.8;
            scrollContainer.value.scrollBy({ left: direction === 'right' ? amount : -amount, behavior: 'smooth' });
        }
    };

    // Update filtered items when filter or items change
    watch(
        [items, filterKey],
        () => {
            filteredItems.value = items.value.filter((item) => filterFn(item, filterKey.value));
            currentIndex.value = 0;
            startAutoplay();
        },
        { immediate: true },
    );

    onMounted(() => {
        checkMobile();
        window.addEventListener('resize', checkMobile);
        startAutoplay();
    });

    onUnmounted(() => {
        window.removeEventListener('resize', checkMobile);
        stopAutoplay();
    });

    return {
        currentIndex,
        isMobile,
        scrollContainer,
        filteredItems,
        goTo,
        next,
        prev,
        scroll,
    };
}
