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
     * Generate a certificate if eligible.
     */
    public function generateIfEligible(User $user, Course $course)
    {
        if ($this->checkEligibility($user, $course)) {
            return $this->generate($user, $course);
        }
        
        return null;
    }

    /**
     * Generate the PDF and save record.
     */
    public function generate(User $user, Course $course)
    {
        // Check if already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        $template = $course->certificateTemplate;

        // Force regeneration if template was updated after certificate creation
        if ($existing && $template && $template->updated_at && $existing->created_at->lt($template->updated_at)) {
             Storage::disk('public')->delete($existing->file_path);
             $existing->delete();
             $existing = null;
        }

        if ($existing) {
            return $existing;
        }

        // Generate a professional unique code: IEE-[RANDOM]-[YEAR]
        $code = 'IEE-' . strtoupper(Str::random(8)) . '-' . date('Y');
        $fileName = 'certificates/' . $user->id . '_' . $course->id . '_' . time() . '.pdf';

        $template = $course->certificateTemplate;
        $templateImageBase64 = null;
        if ($template && $template->template_image) {
            $imageDisk = Storage::disk('public');
            if ($imageDisk->exists($template->template_image)) {
                $imageData = $imageDisk->get($template->template_image);
                $type = strtolower(pathinfo($template->template_image, PATHINFO_EXTENSION));
                $templateImageBase64 = 'data:image/' . $type . ';base64,' . base64_encode($imageData);
            } else {
                 \Illuminate\Support\Facades\Log::warning('Certificate template image missing from disk', [
                    'path' => $template->template_image,
                    'course' => $course->id
                ]);
            }
        }

        $data = [
            'user' => $user,
            'course' => $course,
            'date' => date('d/m/Y'),
            'code' => $code,
            'template' => $template,
            'template_image_base64' => $templateImageBase64,
            // Fallback coordinate data if no template exists
            'is_custom' => $template && $template->template_image ? true : false,
        ];

        // Ensure the directory exists in public storage
        if (!Storage::disk('public')->exists('certificates')) {
            Storage::disk('public')->makeDirectory('certificates');
        }

        $pdf = \Spatie\LaravelPdf\Facades\Pdf::view('pdf.certificate', $data)
            ->format('a4')
            ->landscape()
            ->margins(0, 0, 0, 0)
            ->withBrowsershot(function ($browsershot) {
                $browsershot->noSandbox()
                   ->setOption('args', ['--disable-web-security'])
                   ->windowSize(1122, 794); // Standard A4 at 96dpi
            });
        
        $pdf->save(Storage::disk('public')->path($fileName));

        return Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'code' => $code,
            'file_path' => $fileName,
            'issue_date' => now(),
        ]);
    }
}
