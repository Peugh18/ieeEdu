<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use App\Models\CourseExamAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Check if a user is eligible for a certificate in a specific course.
     */
    public function checkEligibility(User $user, Course $course): bool
    {
        // 1. Check Course Progress
        $allLessons = $course->modules->flatMap->lessons->concat($course->lessons->whereNull('module_id'));
        $lessonIds = $allLessons->pluck('id');
        
        $completedLessonsCount = LessonProgress::where('user_id', $user->id)
            ->whereIn('course_lesson_id', $lessonIds)
            ->where('is_completed', true)
            ->count();
            
        $progressCompleted = ($allLessons->count() > 0) && ($completedLessonsCount === $allLessons->count());
        
        if (!$progressCompleted) {
            return false;
        }

        // 2. Check Exam Status (if the course has quizzes)
        $course->load('quizzes');
        if ($course->quizzes->count() > 0) {
            foreach ($course->quizzes as $quiz) {
                $passed = CourseExamAttempt::where('user_id', $user->id)
                    ->where('course_quiz_id', $quiz->id)
                    ->where('status', 'aprobado')
                    ->exists();
                
                if (!$passed) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get or create a certificate record (without PDF generation).
     */
    public function getOrCreateRecord(User $user, Course $course)
    {
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) return $existing;

        $code = 'IEE-' . strtoupper(Str::random(8)) . '-' . date('Y');
        
        return Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'code' => $code,
            'issue_date' => now(),
            // file_path will be null until generated or we can just use a generic path
            'file_path' => 'certificates/' . $user->id . '_' . $course->id . '.pdf'
        ]);
    }

    /**
     * Generate the PDF response for a certificate.
     */
    public function downloadPdf(Certificate $certificate, $action = 'download')
    {
        $user = $certificate->user;
        $course = $certificate->course;
        $template = $course->certificateTemplate;

        $templateImageBase64 = null;
        if ($template && $template->template_image) {
            $imageDisk = Storage::disk('public');
            if ($imageDisk->exists($template->template_image)) {
                $imageData = $imageDisk->get($template->template_image);
                $type = strtolower(pathinfo($template->template_image, PATHINFO_EXTENSION));
                $templateImageBase64 = 'data:image/' . $type . ';base64,' . base64_encode($imageData);
            }
        }

        $data = [
            'user' => $user,
            'course' => $course,
            'date' => $certificate->issue_date->format('d/m/Y'),
            'code' => $certificate->code,
            'template' => $template,
            'template_image_base64' => $templateImageBase64,
            'is_custom' => $template && $template->template_image ? true : false,
        ];

        $pdf = \Spatie\LaravelPdf\Facades\Pdf::view('pdf.certificate', $data)
            ->format('a4')
            ->landscape()
            ->margins(0, 0, 0, 0)
            ->withBrowsershot(function ($browsershot) {
                $browsershot->noSandbox()
                   ->setOption('args', ['--disable-web-security'])
                   ->windowSize(1122, 794);
            });

        if ($action === 'stream') {
            return $pdf->inline($certificate->code . '.pdf');
        }
        
        return $pdf->download($certificate->code . '.pdf');
    }

    /**
     * DEPRECATED: Use getOrCreateRecord instead for faster submissions.
     */
    public function generateIfEligible(User $user, Course $course)
    {
        if ($this->checkEligibility($user, $course)) {
            return $this->getOrCreateRecord($user, $course);
        }
        return null;
    }
}
