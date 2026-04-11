<?php

$filePath = 'resources/js/pages/admin/CourseEditor.vue';
$content = file_get_contents($filePath);

// 1. Add activeTab
$content = str_replace(
    "const isExamView = ref(false);",
    "const isExamView = ref(false);\nconst activeTab = ref('general');",
    $content
);

// 2. Add Tabs Menu and Tab 1 Wrapper
$tabs_html = <<<'EOF'
            <!-- TABS MENU -->
            <div class="flex overflow-x-auto gap-2 bg-white p-2 rounded-2xl border border-outline-variant/10 shadow-sm mb-6 pb-2 scrollbar-thin">
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Información Básica</button>
                <button @click="activeTab = 'pricing'" :class="activeTab === 'pricing' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Precio y Configuraciones</button>
                <button @click="activeTab = 'details'" :class="activeTab === 'details' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Detalles Adicionales</button>
                <button @click="activeTab = 'instructor'" :class="activeTab === 'instructor' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Instructor y Diploma</button>
                <button @click="activeTab = 'curriculum'" :class="activeTab === 'curriculum' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap flex items-center gap-2">Plan de Estudios <span class="bg-black/20 text-current border border-current text-[10px] px-2 py-0.5 rounded-full">{{ lessons.length }}</span></button>
                <button @click="activeTab = 'exams'" :class="activeTab === 'exams' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Exámenes / Evaluaciones</button>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- TAB: GENERAL -->
                <div v-show="activeTab === 'general'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Información Básica</h2>
                        <p class="text-sm text-on-surface-variant">Define el título y estado principal del programa.</p>
                    </div>
EOF;

$old_grid_start = <<<'EOF'
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <aside class="lg:col-span-4 rounded-2xl border border-outline-variant/10 bg-white p-5 space-y-3">
                    <h2 class="font-semibold">Información del curso</h2>
EOF;

$content = str_replace($old_grid_start, $tabs_html, $content);

// 3. Change pricing area start
$old_pricing_start = <<<'EOF'
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input v-model.number="form.price"
EOF;

$new_pricing_start = <<<'EOF'
                    </div>
                </div>

                <!-- TAB: PRICING -->
                <div v-show="activeTab === 'pricing'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Precios y Ventas</h2>
                        <p class="text-sm text-on-surface-variant">Configura el costo del programa y opciones de descuento.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <input v-model.number="form.price"
EOF;

$content = str_replace($old_pricing_start, $new_pricing_start, $content);

// 5. Details Tab
$old_details_start = <<<'EOF'
                    <hr class="border-outline-variant/20 my-2" />
                    <h3 class="font-semibold text-sm">Detalles específicos</h3>
EOF;

$new_details_start = <<<'EOF'
                </div>

                <!-- TAB: DETAILS -->
                <div v-show="activeTab === 'details'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Detalles Específicos</h2>
                        <p class="text-sm text-on-surface-variant">Fechas, objetivos y requerimientos técnicos del curso.</p>
                    </div>
EOF;

$content = str_replace($old_details_start, $new_details_start, $content);

// 6. Instructor Tab
$old_instructor_start = <<<'EOF'
                    <hr class="border-outline-variant/20 my-2" />
                    <h3 class="font-semibold text-sm">Instructor (Opcional)</h3>
EOF;

$new_instructor_start = <<<'EOF'
                </div>

                <!-- TAB: INSTRUCTOR -->
                <div v-show="activeTab === 'instructor'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Perfil del Instructor</h2>
                        <p class="text-sm text-on-surface-variant">Puedes opcionalmente mostrar quién dicta este curso y el certificado final.</p>
                    </div>
EOF;

$content = str_replace($old_instructor_start, $new_instructor_start, $content);

// 7. End of aside, start of Curriculum
$old_end_aside = <<<'EOF'
                </aside>

                <main class="lg:col-span-8 space-y-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-2 bg-surface-container-low p-1 rounded-2xl border border-outline-variant/10">
                            <button 
                                @click="isExamView = false"
                                class="px-5 py-2 text-sm font-bold rounded-xl transition"
                                :class="!isExamView ? 'bg-white shadow text-primary font-bold' : 'text-on-surface-variant hover:text-primary'"
                            >
                                Contenido
                            </button>
                            <button 
                                @click="isExamView = true"
                                class="px-5 py-2 text-sm font-bold rounded-xl transition"
                                :class="isExamView ? 'bg-white shadow text-primary font-bold' : 'text-on-surface-variant hover:text-primary'"
                            >
                                Exámenes
                            </button>
                        </div>
                        <p v-if="!isExamView" class="text-xs text-on-surface-variant">Masterclass permite solo una clase</p>
                    </div>

                    <div v-if="!isExamView" class="space-y-6">
EOF;

$new_end_aside = <<<'EOF'
                </div>

                <!-- TAB: CURRICULUM -->
                <div v-show="activeTab === 'curriculum'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Plan de Estudios</h2>
                        <p class="text-sm text-on-surface-variant">Construye los módulos, clases y agrega material descargable.</p>
                        <p class="text-xs font-bold text-red-500 mt-2" v-if="isMasterclass">NOTA: Modalidad Masterclass admite exclusivamente 1 sola clase en vivo.</p>
                    </div>

                    <div class="space-y-6 mt-4">
EOF;

$content = str_replace($old_end_aside, $new_end_aside, $content);

// 8. Start of Exams
$old_exams_start = <<<'EOF'
                    <div v-else class="space-y-6 bg-white p-6 rounded-2xl border border-outline-variant/10">
EOF;

$new_exams_start = <<<'EOF'
                </div>

                <!-- TAB: EXAMS -->
                <div v-show="activeTab === 'exams'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Exámenes y Evaluaciones</h2>
                        <p class="text-sm text-on-surface-variant">Crea cuestionarios para medir el aprendizaje.</p>
                    </div>
EOF;

$content = str_replace($old_exams_start, $new_exams_start, $content);

// 9. Clean up closing main tags
$old_end_file = <<<'EOF'
                    </div>
                </main>
            </div>
        </div>
EOF;

$new_end_file = <<<'EOF'
                    </div>
                </div>
            </div>
        </div>
EOF;

$content = str_replace($old_end_file, $new_end_file, $content);

file_put_contents($filePath, $content);

echo "Refactoring PHP script complete.\n";

?>
