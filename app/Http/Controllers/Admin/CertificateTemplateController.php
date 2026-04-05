<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CertificateTemplateController extends Controller
{
    public function edit(Course $course)
    {
        $template = $course->certificateTemplate ?? new CertificateTemplate(['course_id' => $course->id]);
        
        return Inertia::render('admin/CertificateTemplate/Edit', [
            'course' => $course,
            'template' => $template,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        \Illuminate\Support\Facades\Log::info('Updating certificate template', [
            'course' => $course->id,
            'data' => $request->all()
        ]);

        $validated = $request->validate([
            'template_image' => 'nullable|image|max:10240',
            'student_name_X' => 'required|numeric|min:0|max:1',
            'student_name_Y' => 'required|numeric|min:0|max:1',
            'student_name_font_size' => 'required|integer',
            'course_title_X' => 'required|numeric|min:0|max:1',
            'course_title_Y' => 'required|numeric|min:0|max:1',
            'course_title_font_size' => 'required|integer',
            'issue_date_X' => 'required|numeric|min:0|max:1',
            'issue_date_Y' => 'required|numeric|min:0|max:1',
            'issue_date_font_size' => 'required|integer',
            'certificate_code_X' => 'required|numeric|min:0|max:1',
            'certificate_code_Y' => 'required|numeric|min:0|max:1',
            'certificate_code_font_size' => 'required|integer',
            'font_color' => 'required|string',
            'font_family' => 'required|string',
        ]);

        $data = $validated;
        unset($data['template_image']);

        if ($request->hasFile('template_image')) {
            $path = $request->file('template_image')->store('certificates/templates', 'public');
            $data['template_image'] = $path;
        }

        try {
            CertificateTemplate::updateOrCreate(
                ['course_id' => $course->id],
                $data
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Certificate Template Save Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['template' => 'Error al guardar en la base de datos: ' . $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Plantilla de certificado actualizada correctamente.');
    }
}
