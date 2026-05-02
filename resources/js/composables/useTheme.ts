import { ref, onMounted, watch } from 'vue';

type Theme = 'light' | 'dark';

// Global state para el tema
const currentTheme = ref<Theme>('light');

export function useTheme() {
    // Inicializar el tema desde localStorage o preferencias del sistema
    const initTheme = () => {
        if (typeof window !== 'undefined') {
            const storedTheme = localStorage.getItem('iie_theme') as Theme | null;
            if (storedTheme) {
                currentTheme.value = storedTheme;
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                currentTheme.value = 'dark';
            } else {
                currentTheme.value = 'light';
            }
            applyTheme(currentTheme.value);
        }
    };

    // Aplicar la clase al html y guardar en localStorage
    const applyTheme = (theme: Theme) => {
        if (typeof window !== 'undefined') {
            const root = document.documentElement;
            if (theme === 'dark') {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }
            localStorage.setItem('iie_theme', theme);
        }
    };

    // Alternar entre temas
    const toggleTheme = () => {
        currentTheme.value = currentTheme.value === 'light' ? 'dark' : 'light';
    };

    // Escuchar cambios en la variable reactiva
    watch(currentTheme, (newTheme) => {
        applyTheme(newTheme);
    });

    onMounted(() => {
        initTheme();
    });

    return {
        currentTheme,
        toggleTheme,
        initTheme,
        isDark: () => currentTheme.value === 'dark'
    };
}
