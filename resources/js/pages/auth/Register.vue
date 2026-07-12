<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    telefono: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Crea tu cuenta" description="Completa los datos para registrarte">
        <Head title="Registro" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Nombre completo</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="name"
                        v-model="form.name"
                        placeholder="Tu nombre completo"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Correo electrónico</Label>
                    <Input id="email" type="email" required tabindex="2" autocomplete="email" v-model="form.email" placeholder="correo@ejemplo.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="telefono">Número de teléfono</Label>
                    <Input id="telefono" type="tel" tabindex="3" autocomplete="tel" v-model="form.telefono" placeholder="+51 999 999 999" />
                    <InputError :message="form.errors.telefono" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Contraseña</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Tu contraseña"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirmar contraseña</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        tabindex="5"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Repite tu contraseña"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-start space-x-3">
                        <Checkbox id="terms" v-model:checked="form.terms" tabindex="6" />
                        <Label for="terms" class="cursor-pointer select-none text-sm font-normal leading-tight text-on-surface-variant">
                            Acepto los
                            <a :href="route('terms')" target="_blank" class="font-semibold text-primary hover:underline">Términos y Condiciones</a> y
                            la
                            <a :href="route('privacy')" target="_blank" class="font-semibold text-primary hover:underline">Política de Privacidad</a>
                        </Label>
                    </div>
                    <InputError :message="form.errors.terms" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="7" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Crear cuenta
                </Button>
            </div>

            <div class="text-muted-foreground text-center text-sm">
                ¿Ya tienes cuenta?
                <TextLink :href="route('login')" class="underline underline-offset-4" tabindex="8">Inicia sesión</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
