<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseAnswer;
use App\Models\CourseExamAttempt;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\CourseQuestion;
use App\Models\CourseQuiz;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('flujo completo: examen aprobado genera certificado', function () {
    Storage::fake('public');

    // 1. Estudiante
    $student = User::factory()->create(['role' => 'usuario']);

    // 2. Curso publicado con certificado habilitado
    $course = Course::factory()->create([
        'status' => 'PUBLICADO',
        'certificate_enabled' => true,
    ]);

    // 3. Módulo + lecciones
    $module = CourseModule::forceCreate([
        'course_id' => $course->id,
        'title' => 'Módulo 1',
        'sort_order' => 1,
    ]);

    $lesson1 = CourseLesson::forceCreate([
        'course_id' => $course->id,
        'module_id' => $module->id,
        'title' => 'Lección 1',
        'sort_order' => 1,
    ]);

    $lesson2 = CourseLesson::forceCreate([
        'course_id' => $course->id,
        'module_id' => $module->id,
        'title' => 'Lección 2',
        'sort_order' => 2,
    ]);

    // 4. Quiz final con 2 preguntas y respuestas conocidas
    $quiz = CourseQuiz::factory()->create([
        'course_id' => $course->id,
        'minimum_score' => 14,
        'max_attempts' => 2,
    ]);

    $question1 = CourseQuestion::forceCreate([
        'quiz_id' => $quiz->id,
        'question' => 'Pregunta 1',
        'type' => 'multiple_choice',
        'points' => 1,
    ]);

    $answer1Correct = CourseAnswer::forceCreate([
        'question_id' => $question1->id,
        'answer_text' => 'Respuesta correcta 1',
        'is_correct' => true,
    ]);

    CourseAnswer::forceCreate([
        'question_id' => $question1->id,
        'answer_text' => 'Respuesta incorrecta 1',
        'is_correct' => false,
    ]);

    $question2 = CourseQuestion::forceCreate([
        'quiz_id' => $quiz->id,
        'question' => 'Pregunta 2',
        'type' => 'multiple_choice',
        'points' => 1,
    ]);

    $answer2Correct = CourseAnswer::forceCreate([
        'question_id' => $question2->id,
        'answer_text' => 'Respuesta correcta 2',
        'is_correct' => true,
    ]);

    CourseAnswer::forceCreate([
        'question_id' => $question2->id,
        'answer_text' => 'Respuesta incorrecta 2',
        'is_correct' => false,
    ]);

    // 5. Inscripción
    Enrollment::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'progress' => 0,
    ]);

    // 6. Completar todas las lecciones
    LessonProgress::forceCreate([
        'user_id' => $student->id,
        'course_lesson_id' => $lesson1->id,
        'is_completed' => true,
    ]);

    LessonProgress::forceCreate([
        'user_id' => $student->id,
        'course_lesson_id' => $lesson2->id,
        'is_completed' => true,
    ]);

    // 7. Enviar examen con respuestas correctas
    $response = $this->actingAs($student)->post(
        route('student.exams.submit', $quiz),
        [
            'answers' => [
                $question1->id => $answer1Correct->id,
                $question2->id => $answer2Correct->id,
            ],
        ]
    );

    $response->assertRedirect();

    // 8. Assert: intento aprobado
    $attempt = CourseExamAttempt::where('user_id', $student->id)
        ->where('course_quiz_id', $quiz->id)
        ->first();

    expect($attempt)->not->toBeNull();
    expect($attempt->status)->toBe('aprobado');
    expect($attempt->score)->toBeGreaterThanOrEqual(14);

    // 9. Assert: existe certificado
    $this->assertDatabaseHas('certificates', [
        'user_id' => $student->id,
        'course_id' => $course->id,
    ]);

    $certificate = Certificate::where('user_id', $student->id)
        ->where('course_id', $course->id)
        ->first();

    // 10. Assert: descarga de certificado exitosa
    $downloadResponse = $this->actingAs($student)->get(
        route('student.certificates.download', $certificate)
    );

    $downloadResponse->assertStatus(200);
});
