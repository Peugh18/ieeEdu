<script setup lang="ts">
import Navigation from '@/components/landing/Navigation.vue';
import WelcomeFooter from '@/components/landing/WelcomeFooter.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeSection = ref('intro');

const sections = [
    { id: 'intro', title: '1. Introducción y Aceptación' },
    { id: 'propiedad', title: '2. Propiedad Intelectual' },
    { id: 'registro', title: '3. Registro y Uso de Cuentas' },
    { id: 'pagos', title: '4. Precios, Pagos y Reembolsos' },
    { id: 'conducta', title: '5. Conducta y Uso Aceptable' },
    { id: 'reclamos', title: '6. Libro de Reclamaciones y Quejas' },
    { id: 'responsabilidad', title: '7. Limitación de Responsabilidad' },
    { id: 'jurisdiccion', title: '8. Ley Aplicable y Jurisdicción' },
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
    <Head title="Términos y Condiciones — IEE" />

    <div class="min-h-screen bg-surface font-sans text-on-surface">
        <Navigation />

        <!-- Header Section -->
        <header class="relative overflow-hidden bg-surface-container pb-20 pt-32 md:pt-40">
            <div class="absolute left-0 right-0 top-0 h-px bg-gradient-to-r from-transparent via-outline-variant/30 to-transparent"></div>
            <div class="mx-auto max-w-7xl px-6 text-center md:px-8">
                <span class="mb-4 inline-flex rounded-full bg-primary/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.1em] text-primary">
                    Legal & Transparencia
                </span>
                <h1 class="font-serif text-4xl font-bold tracking-tight text-on-surface sm:text-5xl">Términos y Condiciones</h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-on-surface-variant">
                    Última actualización: 24 de junio de 2026. Por favor, lea detenidamente estas condiciones antes de utilizar nuestros servicios.
                </p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-6 py-16 md:px-8 md:py-24">
            <div class="flex flex-col gap-12 lg:flex-row">
                <!-- Sidebar (Sticky Navigation) -->
                <aside class="lg:w-1/4">
                    <div class="sticky top-28 space-y-2 rounded-3xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-sm">
                        <h2 class="mb-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant/75">Contenido</h2>
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

                <!-- Terms Text Details -->
                <article class="flex-1 space-y-12">
                    <section id="intro" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">1. Introducción y Aceptación</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Bienvenido al portal del Instituto de Economía y Empresa (en adelante, "IEE"). Al acceder, registrarse o utilizar nuestro
                            sitio web, aula virtual, catálogo de cursos y otros servicios educativos vinculados, usted acepta cumplir y someterse de
                            manera irrevocable a los presentes Términos y Condiciones.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            El uso de nuestros servicios y la adquisición de nuestros programas académicos se rigen estrictamente por la legislación
                            de la República del Perú. Si usted no está de acuerdo con alguno de los términos aquí estipulados, le instamos a
                            abstenerse de utilizar este sitio web y de registrarse en nuestra plataforma.
                        </p>
                    </section>

                    <section id="propiedad" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">2. Propiedad Intelectual</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Todos los derechos de propiedad intelectual, marcas registradas, nombres comerciales, derechos de autor y patentes sobre
                            los contenidos, materiales audiovisuales, presentaciones escritas, metodologías de estudio, exámenes y software integrados
                            en el portal de IEE son de exclusiva propiedad del Instituto de Economía y Empresa o de sus licenciantes autorizados.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            De acuerdo con la legislación peruana de derechos de autor (Decreto Legislativo N° 822), queda terminantemente prohibida
                            la copia, reproducción, distribución, comercialización, cesión, descarga no autorizada o comunicación pública de los
                            materiales educativos del IEE, bajo apercibimiento de ejercer las acciones legales civiles y penales que correspondan.
                        </p>
                    </section>

                    <section id="registro" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">3. Registro y Uso de Cuentas</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Para acceder a las clases, materiales y certificaciones, el alumno debe registrarse creando una cuenta personal. De
                            conformidad con las leyes peruanas aplicables a las contrataciones electrónicas, usted declara que los datos provistos en
                            el formulario de inscripción son fidedignos y exactos.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            La cuenta es de carácter personal e intransferible. El uso compartido de credenciales de acceso para el aula virtual está
                            estrictamente prohibido y será causal de suspensión inmediata del servicio sin derecho a reembolso.
                        </p>
                    </section>

                    <section id="pagos" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">4. Precios, Pagos y Reembolsos</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Los precios de los cursos, membresías y masterclasses se detallan en la moneda nacional (Soles - S/) e incluyen todos los
                            impuestos de ley aplicables en el Perú (como el Impuesto General a las Ventas - IGV, de corresponder).
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            De conformidad con la normativa peruana en materia de comercio electrónico, los reembolsos o devoluciones de dinero se
                            rigen bajo los siguientes supuestos: el estudiante tendrá derecho a solicitar el reembolso de su dinero siempre que no
                            haya accedido al aula virtual ni visualizado o descargado el material del curso. Una vez iniciado el avance académico o
                            descargado material del programa, el servicio se considera efectivamente prestado, imposibilitando el reembolso.
                        </p>
                    </section>

                    <section id="conducta" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">5. Conducta y Uso Aceptable</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Los estudiantes deben mantener una conducta respetuosa, ética y colaborativa. Queda prohibido utilizar el aula virtual
                            para difundir publicidad no autorizada, cometer actos de plagio académico, insultar o acosar a instructores o compañeros.
                            El IEE se reserva el derecho de dar de baja la cuenta del alumno infractor, aplicando los reglamentos internos de
                            disciplina académica vigentes en el país.
                        </p>
                    </section>

                    <section id="reclamos" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">6. Libro de Reclamaciones y Quejas</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            En cumplimiento de lo dispuesto por el **Código de Protección y Defensa del Consumidor (Ley N° 29571)** de la República
                            del Perú, el IEE pone a disposición de sus alumnos y usuarios un **Libro de Reclamaciones Virtual**.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Cualquier queja o reclamo relacionado con nuestros servicios educativos o procesos de facturación puede ser canalizado
                            formalmente a través de dicho medio. De acuerdo con la **Ley N° 31435**, el plazo máximo de respuesta a su reclamo será de
                            quince (15) días hábiles improrrogables, contados a partir del día siguiente de su presentación.
                        </p>
                    </section>

                    <section id="responsabilidad" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">7. Limitación de Responsabilidad</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            El IEE se compromete a desplegar sus mejores esfuerzos tecnológicos para garantizar la continuidad y el óptimo
                            funcionamiento del aula virtual. Sin embargo, no se hace responsable por interrupciones debidas a fuerza mayor, fallas en
                            la red de telecomunicaciones del usuario o suspensiones temporales requeridas para el mantenimiento y actualización del
                            sistema.
                        </p>
                    </section>

                    <section id="jurisdiccion" class="scroll-mt-28 space-y-4">
                        <h2 class="font-serif text-2xl font-bold text-on-surface">8. Ley Aplicable y Jurisdicción</h2>
                        <div class="h-1 w-12 rounded bg-primary"></div>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Estos Términos y Condiciones se rigen e interpretan de acuerdo con las leyes vigentes de la República del Perú.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Toda controversia derivada de la interpretación o ejecución de este contrato, o relacionada con el uso de la plataforma
                            del IEE, se someterá a la jurisdicción y competencia exclusiva de los jueces y tribunales del distrito judicial de
                            **Trujillo, La Libertad, Perú**, renunciando expresamente el usuario a cualquier otro fuero.
                        </p>
                        <p class="text-sm leading-relaxed text-on-surface-variant">
                            Para consultas legales adicionales, puede escribir a: <strong>legal@iee.edu.pe</strong> o
                            <strong>info@iee.edu.pe</strong>.
                        </p>
                    </section>
                </article>
            </div>
        </main>

        <WelcomeFooter />
    </div>
</template>
