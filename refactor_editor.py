import re

with open('resources/js/pages/admin/CourseEditor.vue', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add activeTab
content = content.replace(
    'const isExamView = ref(false);',
    "const isExamView = ref(false);\nconst activeTab = ref('general');"
)

# 2. Add Tabs Menu and Tab 1 Wrapper
tabs_html = """
            <!-- TABS MENU -->
            <div class="flex overflow-x-auto gap-2 bg-white p-2 rounded-2xl border border-outline-variant/10 shadow-sm mb-6 pb-2 scrollbar-thin">
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Información Básica</button>
                <button @click="activeTab = 'pricing'" :class="activeTab === 'pricing' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Precio y Configuraciones</button>
                <button @click="activeTab = 'details'" :class="activeTab === 'details' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Detalles Adicionales</button>
                <button @click="activeTab = 'instructor'" :class="activeTab === 'instructor' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Instructor y Diploma</button>
                <button @click="activeTab = 'curriculum'" :class="activeTab === 'curriculum' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap flex items-center gap-2">Plan de Estudios <span class="bg-white/20 text-current border border-current text-[10px] px-2 py-0.5 rounded-full">{{ lessons.length }}</span></button>
                <button @click="activeTab = 'exams'" :class="activeTab === 'exams' ? 'bg-[#57572A] text-white shadow-md' : 'text-on-surface hover:bg-surface-container-low'" class="px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all whitespace-nowrap">Exámenes / Evaluaciones</button>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <!-- TAB: GENERAL -->
                <div v-show="activeTab === 'general'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Información Básica</h2>
                        <p class="text-sm text-on-surface-variant">Define el título y estado principal del programa.</p>
                    </div>
"""

old_grid_start = """            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <aside class="lg:col-span-4 rounded-2xl border border-outline-variant/10 bg-white p-5 space-y-3">
                    <h2 class="font-semibold">Información del curso</h2>"""

content = content.replace(old_grid_start, tabs_html)

# 3. Change pricing area start
old_pricing_start = """                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input v-model.number="form.price\""""

new_pricing_start = """                    </div>
                </div>

                <!-- TAB: PRICING -->
                <div v-show="activeTab === 'pricing'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Precios y Ventas</h2>
                        <p class="text-sm text-on-surface-variant">Configura el costo del programa y opciones de descuento.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <input v-model.number="form.price\""""

content = content.replace(old_pricing_start, new_pricing_start)

# 4. Status and Category and Image -> let's map them to Pricing or General?
# Wait! They were right after Pricing. Let's move them back to General or keep them in Pricing.
# I'll just leave them inside the Pricing Tab for now since they are part of "Configurations".

# 5. Details Tab
old_details_start = """                    <hr class="border-outline-variant/20 my-2" />
                    <h3 class="font-semibold text-sm">Detalles específicos</h3>"""

new_details_start = """                </div>

                <!-- TAB: DETAILS -->
                <div v-show="activeTab === 'details'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Detalles Específicos</h2>
                        <p class="text-sm text-on-surface-variant">Fechas, objetivos y requerimientos técnicos del curso.</p>
                    </div>"""

content = content.replace(old_details_start, new_details_start)

# 6. Instructor Tab
old_instructor_start = """                    <hr class="border-outline-variant/20 my-2" />
                    <h3 class="font-semibold text-sm">Instructor (Opcional)</h3>"""

new_instructor_start = """                </div>

                <!-- TAB: INSTRUCTOR -->
                <div v-show="activeTab === 'instructor'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Perfil del Instructor</h2>
                        <p class="text-sm text-on-surface-variant">Puedes opcionalmente mostrar quién dicta este curso.</p>
                    </div>"""

content = content.replace(old_instructor_start, new_instructor_start)

# 7. End of aside, start of Curriculum
old_end_aside = """                </aside>

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

                    <div v-if="!isExamView" class="space-y-6">"""

new_end_aside = """                </div>

                <!-- TAB: CURRICULUM -->
                <div v-show="activeTab === 'curriculum'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Plan de Estudios</h2>
                        <p class="text-sm text-on-surface-variant">Construye los módulos, clases y agrega material descargable.</p>
                        <p class="text-xs font-bold text-red-500 mt-2" v-if="isMasterclass">NOTA: Modalidad Masterclass admite exclusivamente 1 sola clase en vivo.</p>
                    </div>

                    <div class="space-y-6 mt-4">"""

content = content.replace(old_end_aside, new_end_aside)

# 8. Start of Exams
old_exams_start = """                    <div v-else class="space-y-6 bg-white p-6 rounded-2xl border border-outline-variant/10">"""
new_exams_start = """                </div>

                <!-- TAB: EXAMS -->
                <div v-show="activeTab === 'exams'" class="rounded-2xl border border-outline-variant/10 bg-white p-6 md:p-8 space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                    <div>
                        <h2 class="text-xl font-serif font-bold text-on-surface mb-2">Exámenes y Evaluaciones</h2>
                        <p class="text-sm text-on-surface-variant">Crea cuestionarios para medir el aprendizaje.</p>
                    </div>"""

content = content.replace(old_exams_start, new_exams_start)

# 9. Clean up any closing tags for `main`. Wait `main></div>` isn't used anymore because we removed `<main>` tag.
# We had `</main>` and `</div>` at the very end.
old_end_file = """                    </div>
                </main>
            </div>
        </div>"""

new_end_file = """                    </div>
                </div>
            </div>
        </div>"""

content = content.replace(old_end_file, new_end_file)

with open('resources/js/pages/admin/CourseEditor.vue', 'w', encoding='utf-8') as f:
    f.write(content)

print("Refactoring applied!")
