<script setup lang="ts">
import Navigation from '@/components/landing/Navigation.vue';
import WelcomeFooter from '@/components/landing/WelcomeFooter.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeSection = ref('recoleccion');

const sections = [
    { id: 'marco', title: '1. Marco Legal (Ley N° 29733)' },
    { id: 'banco', title: '2. Banco de Datos Personales' },
    { id: 'recoleccion', title: '3. Recolección de Datos' },
    { id: 'uso', title: '4. Finalidades del Tratamiento' },
    { id: 'arco', title: '5. Derechos ARCO y Ejercicio' },
    { id: 'compartir', title: '6. Transferencia y Flujo Transfronterizo' },
    { id: 'cookies', title: '7. Uso de Cookies' },
    { id: 'contacto', title: '8. Dudas y Consultas' },
];

function scrollToSection(id: string) {
    activeSection.value = id;
    const element = document.getElementById(id);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<template>
    <Head title="Política de Privacidad — IEE" />

    <div class="min-h-screen bg-surface font-sans text-on-surface">
        <Navigation />

        <!-- Header Section -->
        <header class="relative overflow-hidden bg-surface-container pb-20 pt-32 md:pt-40">
            <div class="absolute left-0 right-0 top-0 h-px bg-gradient-to-r from-transparent via-outline-variant/30 to-transparent"></div>
            <div class="mx-auto max-w-7xl px-6 text-center md:px-8">
                <span class="mb-4 inline-flex rounded-full bg-primary/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.1em] text-primary">
                    Privacidad & Seguridad
                </span>
                <h1 class="font-serif text-4xl font-bold tracking-tight text-on-surface sm:text-5xl">Política de Privacidad</h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-on-surface-variant">
                    Última actualización: 24 de junio de 2026. Tu privacidad y la protección de tus datos personales son de suma importancia para
                    nosotros.
                </p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-6 py-16 md:px-8 md:py-24">
            <div class="flex flex-col gap-12 lg:flex-row">
                <!-- Sidebar (Sticky Navigation) -->
                <aside class="lg:w-1/4">
                    <div class="sticky top-28 space-y-2 rounded-3xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-sm">
                        <h2 class="mb-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant/75">Secciones</h2>
                        <nav class="flex flex-col gap-1">
                            <button
                                v-for="section in sections"
                                :key="section.id"
                                @click="scrollToSection(section.id)"
                                :class="[
                                    'w-full rounded-xl border-l-2 px-4 py-2.5 text-left text-xs font-semibold transition-all duration-200',
                                    activeSection === section.id
                                        ? 'border-primary bg-primary/5 text-primary'
                                        : 'border-transparent text-on-surface-variant hover:bg-outline-variant/10 hover:text-on-surface',
                                ]"
                            >
                                {{ section.title }}
                            </button>
                        </nav>
                    </div>
                </aside>

                <!-- Privacy Text Details -->
                <article class="flex-1 space-y-12">
                    <section id="marco" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">1. Marco Legal (Ley N° 29733)</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            La presente Política de Privacidad describe el tratamiento que el Instituto de Economía y Empresa (en adelante, "IEE")
                            realiza sobre los datos personales proporcionados por sus usuarios y alumnos.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Esta política cumple estrictamente con las disposiciones de la **Ley N° 29733 - Ley de Protección de Datos Personales
                            (LPDP)** de la República del Perú, su Reglamento aprobado por **Decreto Supremo N° 003-2013-JUS**, y las directivas
                            emitidas por la Autoridad Nacional de Protección de Datos Personales (ANPD).
                        </p>
                    </section>

                    <section id="banco" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">2. Banco de Datos Personales</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Toda la información personal recopilada será incorporada, almacenada y tratada en el banco de datos de titularidad del
                            **Instituto de Economía y Empresa**, con domicilio legal en la ciudad de Trujillo, La Libertad, Perú.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Dicho banco de datos denominado **"Alumnos y Usuarios"** se encuentra debidamente inscrito (o en proceso de inscripción
                            formal) ante el Registro Nacional de Protección de Datos Personales a cargo del Ministerio de Justicia y Derechos Humanos
                            de la República del Perú.
                        </p>
                    </section>

                    <section id="recoleccion" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">3. Recolección de Datos</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            El IEE recopila datos personales proporcionados directamente por usted al registrarse, comprar un curso, matricularse en
                            una masterclass o solicitar una consultoría especializada. Los datos recopilados incluyen: nombre, dirección de correo
                            electrónico, número de teléfono (WhatsApp), país, historial de navegación dentro del aula virtual, respuestas a exámenes y
                            calificaciones obtenidas.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            El alumno garantiza la veracidad de los datos provistos. Los datos personales son obligatorios para prestar el servicio
                            educativo; la negativa a facilitarlos imposibilitará la creación de la cuenta de estudiante y el acceso al aula virtual.
                        </p>
                    </section>

                    <section id="uso" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">4. Finalidades del Tratamiento</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            De conformidad con la LPDP peruana, sus datos personales serán tratados exclusivamente para las siguientes finalidades
                            principales:
                        </p>
                        <ul class="list-disc space-y-2 pl-5 text-sm leading-relaxed text-on-surface-variant">
                            <li>Gestionar la creación y administración de su cuenta de alumno.</li>
                            <li>Operar la plataforma virtual y registrar sus avances, exámenes, notas y asistencia académica.</li>
                            <li>Procesar la facturación de las compras e inscripciones y verificar los comprobantes de pago.</li>
                            <li>Emitir, registrar y validar de forma oficial los certificados académicos a nombre del IEE.</li>
                        </ul>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Adicionalmente, y de forma secundaria (solo si el usuario brinda su consentimiento libre y explícito en los formularios
                            autorizados), el IEE tratará los datos para enviar boletines informativos, novedades comerciales y promociones de nuevos
                            cursos o programas académicos. Usted podrá revocar este consentimiento publicitario en cualquier momento.
                        </p>
                    </section>

                    <section id="arco" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">5. Derechos ARCO y Ejercicio</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Usted tiene derecho a ejercer sus derechos de **Acceso, Rectificación, Cancelación y Oposición (Derechos ARCO)** previstos
                            en la legislación peruana.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Para ejercer cualquiera de estos derechos, deberá dirigir una solicitud escrita al Oficial de Protección de Datos
                            Personales del IEE mediante correo electrónico a **privacidad@iee.edu.pe**. Con el fin de verificar su identidad de
                            acuerdo al reglamento de la LPDP, deberá adjuntar una copia legible de su DNI, Pasaporte o Carnet de Extranjería.
                        </p>
                    </section>

                    <section id="compartir" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">6. Transferencia y Flujo Transfronterizo</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            El IEE no comparte sus datos personales con terceros con fines de comercialización directa. Sin embargo, para la correcta
                            operatividad técnica de la plataforma, los datos personales pueden ser tratados por terceros proveedores que actúan como
                            encargados del tratamiento (por ejemplo, pasarelas de pago y proveedores de infraestructura en la nube).
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Debido a que nuestros servidores y servicios de base de datos están alojados en plataformas internacionales seguras en la
                            nube (como AWS, DigitalOcean o Google Cloud), se produce una **transferencia internacional de datos personales (flujo
                            transfronterizo)** a los Estados Unidos de América u otros países de destino técnico del proveedor. IEE garantiza que
                            estos proveedores cuentan con políticas de privacidad alineadas a altos estándares internacionales de protección.
                        </p>
                    </section>

                    <section id="cookies" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">7. Uso de Cookies</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            El portal de IEE utiliza cookies esenciales y de análisis para optimizar su experiencia de navegación, recordar sus
                            credenciales de sesión activa y analizar estadísticas de tráfico web. Usted puede configurar su navegador para restringir
                            o bloquear el uso de cookies, pero esto podría limitar el acceso a ciertas herramientas interactivas del aula virtual.
                        </p>
                    </section>

                    <section id="contacto" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">8. Dudas y Consultas</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Si tiene alguna duda o consulta respecto al tratamiento de sus datos personales bajo la ley peruana, por favor póngase en
                            contacto con nosotros:
                        </p>
                        <ul class="list-disc space-y-2 pl-5 text-sm leading-relaxed text-on-surface-variant">
                            <li><strong>Correo electrónico:</strong> privacidad@iee.edu.pe / info@iee.edu.pe</li>
                            <li><strong>Oficinas:</strong> Trujillo, La Libertad - Perú</li>
                        </ul>
                    </section>
                </article>
            </div>
        </main>

        <WelcomeFooter />
    </div>
</template>
