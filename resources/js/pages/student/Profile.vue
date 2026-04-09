<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { User, BookOpen, Crown, Settings as SettingsIcon, LogOut, ChevronRight, GraduationCap } from 'lucide-vue-next';

interface Props {
    enrolledCourses: any[];
    activeSubscription: any;
}
const props = defineProps<Props>();

const page = usePage<any>();
const authUser = computed(() => page.props.auth.user);

const activeTab = ref('perfil');

const profileForm = useForm({
    name: authUser.value.name,
    avatar: null as File | null,
});

const passwordForm = useForm({
    password: '',
    password_confirmation: '',
});

const avatarPreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const handleAvatarChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        profileForm.avatar = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const updateProfileData = () => {
    profileForm.post(route('student.profile.custom.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Updated successfully
        }
    });
};

const updatePasswordData = () => {
    passwordForm.post(route('student.profile.custom.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        }
    });
};

const tabs = [
    { id: 'perfil', label: 'Mi Perfil', icon: User },
    { id: 'cursos', label: 'Mis Cursos', icon: BookOpen },
    { id: 'premium', label: 'Suscripción', icon: Crown },
    { id: 'config', label: 'Configuración', icon: SettingsIcon },
];

</script>

<template>
    <Head title="Perfil de Usuario - IEE" />

    <AppLayout>
        <div class="p-6 md:p-12 max-w-[1400px] mx-auto min-h-[85vh]">
            
            <div class="flex flex-col lg:flex-row gap-8 mt-4 relative items-start">
                
                <!-- Inner Sidebar (SaaS Style) -->
                <aside class="w-full lg:w-[300px] shrink-0 sticky top-24">
                    <div class="bg-white border border-outline-variant/30 rounded-3xl shadow-sm p-6 space-y-6">
                        
                        <!-- Quick Profile Header -->
                        <div class="flex flex-col items-center text-center space-y-3 pb-6 border-b border-outline-variant/20">
                                <div class="relative group cursor-pointer" @click="fileInput?.click()">
                                    <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleAvatarChange" />
                                    <img 
                                        :src="avatarPreview || (authUser.avatar ? '/storage/'+authUser.avatar : `https://ui-avatars.com/api/?name=${encodeURIComponent(authUser.name)}&background=57572A&color=fff`)" 
                                        class="w-24 h-24 rounded-full object-cover border-[6px] border-white shadow-xl bg-surface transition-opacity group-hover:opacity-75"
                                    />
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="bg-black/50 text-white rounded-full p-2">
                                            <SettingsIcon class="w-5 h-5" />
                                        </div>
                                    </div>
                                    <div v-if="props.activeSubscription" class="absolute -bottom-1 -right-1 bg-gradient-to-r from-amber-400 to-amber-600 text-white p-2 rounded-full ring-4 ring-white" title="Premium Activo">
                                    <Crown class="w-4 h-4" />
                                </div>
                            </div>
                            <div class="mt-2">
                                <h3 class="font-serif font-bold text-xl text-on-surface">{{ authUser.name }}</h3>
                                <p class="text-xs text-on-surface-variant font-medium mt-1">{{ authUser.email }}</p>
                            </div>
                            <div class="flex gap-2 justify-center mt-2">
                                <span class="bg-green-100 text-green-700 font-bold text-[10px] uppercase tracking-widest px-3 py-1 rounded-full">
                                    Alumno Activo
                                </span>
                            </div>
                        </div>

                        <!-- Sidebar Nav -->
                        <nav class="space-y-2">
                            <button 
                                v-for="tab in tabs" :key="tab.id"
                                @click="activeTab = tab.id"
                                class="flex w-full items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all"
                                :class="activeTab === tab.id ? 'bg-primary text-white shadow-md' : 'text-on-surface hover:bg-outline-variant/10'"
                            >
                                <component :is="tab.icon" class="w-5 h-5" :class="activeTab === tab.id ? 'text-white' : 'opacity-60'" />
                                {{ tab.label }}
                            </button>
                        </nav>

                        <!-- Logout Action -->
                        <div class="pt-6 border-t border-outline-variant/20">
                            <Link 
                                :href="route('logout')" 
                                method="post" as="button"
                                class="flex w-full items-center justify-center gap-2 text-red-600 hover:bg-red-50 p-3.5 rounded-2xl text-xs font-bold uppercase tracking-widest border border-red-100 transition-colors group"
                            >
                                <LogOut class="w-4 h-4 opacity-70 group-hover:opacity-100" />
                                Cerrar Sesión
                            </Link>
                        </div>
                    </div>
                </aside>

                <!-- Dynamic Content Right -->
                <main class="flex-1 w-full min-w-0">
                    <div class="bg-white rounded-3xl border border-outline-variant/30 shadow-sm p-8 lg:p-12 min-h-[500px]">
                        
                        <!-- Tab: MI PERFIL -->
                        <section v-if="activeTab === 'perfil'" class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div>
                                <h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Información Académica</h2>
                                <p class="text-on-surface-variant text-sm">Resumen de tu formación en el instituto.</p>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-surface p-6 rounded-2xl border border-outline-variant/20 flex gap-4 items-center">
                                    <div class="p-4 bg-primary/10 text-primary rounded-2xl shrink-0"><GraduationCap class="w-6 h-6"/></div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">Cursos Activos</p>
                                        <p class="font-black text-2xl text-on-surface mt-1">{{ props.enrolledCourses.filter(c => c.progress < 100).length }}</p>
                                    </div>
                                </div>
                                <div class="bg-surface p-6 rounded-2xl border border-outline-variant/20 flex gap-4 items-center">
                                    <div class="p-4 bg-green-100 text-green-700 rounded-2xl shrink-0"><BookOpen class="w-6 h-6"/></div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">Cursos Completados</p>
                                        <p class="font-black text-2xl text-on-surface mt-1">{{ props.enrolledCourses.filter(c => c.progress === 100).length }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-12 hidden lg:block border-t border-outline-variant/20"></div>
                            
                            <!-- Profile Information Edit -->
                            <div class="mt-8 pt-8 lg:mt-0 lg:pt-0">
                                <h3 class="text-xl font-serif font-bold text-on-surface mb-6">Detalles de la Cuenta</h3>
                                <form @submit.prevent="updateProfileData" class="space-y-6 max-w-xl bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-8">
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Nombre Completo</label>
                                        <input 
                                            v-model="profileForm.name" type="text" 
                                            class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                        />
                                        <p v-if="profileForm.errors.name" class="text-red-500 text-xs mt-1">{{ profileForm.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Correo Electrónico</label>
                                        <input 
                                            :value="authUser.email" type="email" disabled
                                            class="w-full bg-surface-container border border-outline-variant/20 rounded-xl px-4 py-3 text-sm text-on-surface-variant opacity-70 cursor-not-allowed"
                                        />
                                        <p class="text-xs text-on-surface-variant/70">El email no puede ser modificado aquí.</p>
                                    </div>
                                    <div class="pt-4">
                                        <button 
                                            type="submit" 
                                            :disabled="profileForm.processing"
                                            class="inline-flex bg-primary text-white font-bold text-xs uppercase tracking-widest px-8 py-3 rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50"
                                        >
                                            {{ profileForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </section>

                        <!-- Tab: MIS CURSOS -->
                        <section v-if="activeTab === 'cursos'" class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Mis Cursos</h2>
                                    <p class="text-on-surface-variant text-sm">Tus programas de especialización activos y completados.</p>
                                </div>
                                <Link :href="route('student.explore.courses')" class="hidden sm:inline-flex text-[10px] font-bold uppercase tracking-widest text-primary border border-primary/20 px-4 py-2 rounded-xl hover:bg-primary/5 transition-all">Explorar más</Link>
                            </div>

                            <div v-if="props.enrolledCourses.length > 0" class="space-y-4">
                                <article 
                                    v-for="course in props.enrolledCourses" :key="course.id"
                                    class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl border border-outline-variant/30 hover:border-primary/40 transition-colors bg-surface-container-lowest group"
                                >
                                    <img :src="course.image" class="w-full sm:w-48 h-32 object-cover rounded-xl shrink-0" />
                                    <div class="flex-1 space-y-4 w-full">
                                        <div>
                                            <h4 class="font-serif font-bold text-lg leading-tight group-hover:text-primary transition-colors text-on-surface">{{ course.title }}</h4>
                                            <p class="text-xs text-on-surface-variant font-medium mt-1">Última actividad: <span class="text-on-surface text-primary">{{ course.last_lesson }}</span></p>
                                        </div>
                                        
                                        <div class="flex items-center gap-4">
                                            <div class="flex-1">
                                                <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest mb-1" :class="course.progress === 100 ? 'text-green-600' : 'text-primary'">
                                                    <span>Progreso</span>
                                                    <span>{{ course.progress }}%</span>
                                                </div>
                                                <div class="h-1.5 w-full bg-surface-container rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full transition-all duration-1000" :class="course.progress === 100 ? 'bg-green-500' : 'bg-primary'" :style="`width: ${course.progress}%`"></div>
                                                </div>
                                            </div>
                                            <Link 
                                                :href="route('student.classroom', { course: course.slug })"
                                                class="shrink-0 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all"
                                                :class="course.progress === 100 ? 'bg-surface border border-outline-variant hover:bg-outline-variant/10 text-on-surface' : 'bg-primary text-on-primary hover:opacity-90'"
                                            >
                                                {{ course.progress === 100 ? 'Repasar' : 'Continuar' }}
                                            </Link>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div v-else class="py-16 text-center border-2 border-dashed border-outline-variant/40 rounded-3xl space-y-4">
                                <BookOpen class="w-12 h-12 text-on-surface-variant/40 mx-auto" />
                                <h4 class="text-xl font-serif font-bold text-on-surface">No tienes cursos</h4>
                                <Link :href="route('student.explore.courses')" class="inline-flex mt-2 text-xs font-bold uppercase text-primary hover:underline">Ver Catálogo</Link>
                            </div>
                        </section>

                        <!-- Tab: PREMIUM -->
                        <section v-if="activeTab === 'premium'" class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div>
                                <h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Suscripción</h2>
                                <p class="text-on-surface-variant text-sm">Administra tu membresía actual.</p>
                            </div>
                            
                            <div v-if="props.activeSubscription" class="bg-gradient-to-br from-gray-900 to-black text-white p-8 lg:p-10 rounded-3xl relative overflow-hidden shadow-2xl">
                                <div class="relative z-10 space-y-6">
                                    <div class="inline-flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest text-amber-400">Suscripción Activa</div>
                                    <h3 class="text-4xl sm:text-5xl font-serif font-bold italic text-white capitalize">Plan {{ props.activeSubscription.type }}</h3>
                                    <div class="grid grid-cols-2 gap-4 border-t border-white/10 pt-6">
                                        <div>
                                            <p class="text-[10px] text-gray-400 uppercase font-bold mb-1">Próxima Renovación</p>
                                            <p class="font-medium text-amber-400">{{ new Date(props.activeSubscription.end_date).toLocaleDateString() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/30 text-center space-y-6">
                                <div class="w-16 h-16 bg-gradient-to-tr from-[#D4AF37] to-[#AA7C11] rounded-full mx-auto flex items-center justify-center">
                                    <Crown class="w-8 h-8 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-2xl font-serif font-bold text-on-surface">No tienes plan activo</h3>
                                </div>
                                <Link :href="route('planes')" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#D4AF37] to-[#AA7C11] text-white px-8 py-3.5 rounded-xl text-xs font-bold uppercase shadow-lg hover:-translate-y-1 transition-all">
                                    Ver Planes Premium
                                </Link>
                            </div>
                        </section>

                        <!-- Tab: CONFIGURACIÓN -->
                        <section v-if="activeTab === 'config'" class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div>
                                <h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Seguridad de la Cuenta</h2>
                                <p class="text-on-surface-variant text-sm">Actualiza tu contraseña para mantener tu cuenta protegida.</p>
                            </div>
                            
                            <form @submit.prevent="updatePasswordData" class="space-y-6 max-w-xl bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Nueva Contraseña</label>
                                    <input 
                                        v-model="passwordForm.password" type="password" placeholder="Mínimo 8 caracteres"
                                        class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                    />
                                    <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">Confirmar Contraseña</label>
                                    <input 
                                        v-model="passwordForm.password_confirmation" type="password" placeholder="Repite tu nueva contraseña"
                                        class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                    />
                                </div>
                                
                                <div class="pt-4">
                                    <button 
                                        type="submit" 
                                        :disabled="passwordForm.processing"
                                        class="inline-flex bg-primary text-white font-bold text-xs uppercase tracking-widest px-8 py-3 rounded-xl hover:opacity-90 transition-opacity disabled:opacity-50"
                                    >
                                        {{ passwordForm.processing ? 'Actualizando...' : 'Actualizar Contraseña' }}
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>
                </main>

            </div>
        </div>
    </AppLayout>
</template>
