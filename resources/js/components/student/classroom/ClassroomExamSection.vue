<script setup lang="ts">
import { Trophy, XCircle, AlertCircle, Download, ArrowRight } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import type { Course, QuizStats } from '@/types/classroom';

defineProps<{
    course: Course;
    quizStats: QuizStats | null;
    localAllCompleted: boolean;
    localCompletedLength: number;
    allLessonsCount: number;
}>();
</script>

<template>
    <div class="min-h-full flex flex-col items-center justify-center p-4 md:p-8 lg:p-24 bg-background animate-in fade-in duration-700">
         <div class="max-w-4xl w-full p-6 md:p-16 bg-white rounded-2xl md:rounded-[4rem] border border-outline-variant/20 text-center shadow-[0_40px_100px_rgba(87,87,42,0.1)] relative overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent pointer-events-none opacity-40"></div>
              
              <div class="relative z-10 space-y-6 md:space-y-10">
                   <div class="inline-flex p-5 md:p-8 rounded-2xl md:rounded-[2.5rem] border shadow-inner" 
                        :class="quizStats?.passed ? 'bg-emerald-500/5 border-emerald-500/20' : (quizStats?.attempts_left === 0 ? 'bg-red-500/5 border-red-500/20' : (localAllCompleted ? 'bg-primary/5 border-primary/20' : 'bg-orange-500/5 border-orange-500/10'))">
                      <Trophy v-if="quizStats?.passed" class="w-12 h-12 md:w-20 md:h-20 text-emerald-600" />
                      <XCircle v-else-if="quizStats?.attempts_left === 0" class="w-12 h-12 md:w-20 md:h-20 text-red-600" />
                      <Trophy v-else-if="localAllCompleted" class="w-12 h-12 md:w-20 md:h-20 text-[#D4AF37]" />
                      <AlertCircle v-else class="w-12 h-12 md:w-20 md:h-20 text-orange-400" />
                  </div>

                  <div class="space-y-3 md:space-y-6">
                     <h2 class="text-2xl md:text-4xl lg:text-6xl font-serif font-bold italic text-on-background">
                         {{ quizStats?.passed ? '¡Certificación Obtenida!' : (quizStats?.attempts_left === 0 ? 'Evaluación Finalizada' : 'Certificación Final') }}
                     </h2>
                     
                     <p v-if="quizStats?.passed" class="text-on-surface-variant italic font-serif text-sm md:text-xl max-w-2xl mx-auto leading-relaxed">
                        Has aprobado exitosamente la evaluación final de este curso. Tu certificado de aprobación ya se encuentra disponible para su descarga en formato oficial.
                     </p>
                     <p v-else-if="quizStats?.attempts_left === 0" class="text-on-surface-variant italic font-serif text-sm md:text-xl max-w-2xl mx-auto leading-relaxed">
                        Has agotado todos los intentos permitidos para esta evaluación sin alcanzar la nota mínima requerida. Por favor, ponte en contacto con la coordinación académica para solicitar una re-evaluación.
                     </p>
                     <p v-else-if="localAllCompleted" class="text-on-surface-variant italic font-serif text-sm md:text-xl max-w-2xl mx-auto leading-relaxed">
                        Excelencia académica alcanzada. Has completado exitosamente todos los módulos curriculares. Ahora puedes proceder con la evaluación final para acreditar tus conocimientos.
                     </p>
                     <p v-else class="text-on-surface-variant italic font-serif text-sm md:text-xl max-w-2xl mx-auto leading-relaxed">
                        Aún no cumples con el 100% de la visualización del currículo académico. Por favor, asegúrate de ver todas las lecciones antes de rendir el examen.
                     </p>
                  </div>
                  
                  <div v-if="!localAllCompleted && !(quizStats?.attempts_left === 0) && !quizStats?.passed" class="pt-4 md:pt-10">
                      <div class="px-5 py-4 md:px-10 md:py-6 bg-background rounded-xl md:rounded-[2rem] border border-outline-variant/20 shadow-inner inline-block">
                          <p class="text-[9px] md:text-[10px] font-black text-primary/40 uppercase tracking-[0.2em] md:tracking-[0.3em] mb-2 md:mb-3">Progreso del programa</p>
                          <div class="flex items-center gap-4 md:gap-6">
                              <div class="text-xl md:text-3xl font-serif font-bold italic text-primary tabular-nums">
                                  {{ localCompletedLength }} / {{ allLessonsCount }}
                              </div>
                              <div class="w-24 md:w-48 h-2 bg-outline-variant/20 rounded-full overflow-hidden">
                                  <div class="h-full bg-[#D4AF37] rounded-full" :style="{ width: `${(localCompletedLength / allLessonsCount) * 100}%` }"></div>
                              </div>
                          </div>
                      </div>
                  </div>
                  
                  <div class="pt-4 md:pt-8">
                    <template v-if="quizStats?.passed">
                        <a v-if="quizStats.certificate_url" :href="quizStats.certificate_url" target="_blank" class="inline-flex px-8 py-4 md:px-16 md:py-6 bg-emerald-600 text-white font-black text-[10px] md:text-[12px] uppercase tracking-[0.2em] md:tracking-[0.25em] rounded-xl md:rounded-2xl hover:bg-emerald-700 hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-emerald-900/20 group">
                            Descargar Certificado
                            <Download class="w-4 h-4 md:w-5 md:h-5 ml-3 md:ml-4" />
                        </a>
                        <span v-else class="inline-flex px-8 py-4 md:px-16 md:py-6 bg-emerald-50 text-emerald-600 font-black text-[10px] md:text-[12px] uppercase tracking-[0.2em] md:tracking-[0.25em] rounded-xl md:rounded-2xl italic border border-emerald-100">
                            Procesando Certificado...
                        </span>
                    </template>
                    <template v-else-if="quizStats?.attempts_left === 0">
                        <button disabled class="px-8 py-4 md:px-16 md:py-6 bg-rose-50 text-rose-500 font-black text-[10px] md:text-[12px] uppercase tracking-[0.2em] md:tracking-[0.25em] rounded-xl md:rounded-2xl cursor-not-allowed border border-rose-100">
                            Evaluación Bloqueada (Sin Intentos)
                        </button>
                    </template>
                    <template v-else>
                        <Link v-if="localAllCompleted" :href="course.quizzes?.length ? route('student.exams.take', { quiz: course.quizzes[0].id }) : '#'" class="inline-flex px-8 py-4 md:px-16 md:py-6 bg-primary text-white font-black text-[10px] md:text-[12px] uppercase tracking-[0.2em] md:tracking-[0.25em] rounded-xl md:rounded-2xl hover:bg-on-background hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-primary/20 group">
                            Iniciar Evaluación (Intentos: {{ quizStats ? quizStats.attempts_left : course.quizzes[0]?.max_attempts }})
                            <ArrowRight class="w-4 h-4 md:w-5 md:h-5 ml-3 md:ml-4 group-hover:translate-x-2 transition-transform" />
                        </Link>
                        <button v-else disabled class="px-8 py-4 md:px-16 md:py-6 bg-outline-variant/20 text-outline-variant font-black text-[10px] md:text-[12px] uppercase tracking-[0.2em] md:tracking-[0.25em] rounded-xl md:rounded-2xl cursor-not-allowed">
                            Evaluación Bloqueada
                        </button>
                    </template>
                  </div>
              </div>
         </div>
    </div>
</template>
