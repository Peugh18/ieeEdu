<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'course_id',
        'template_image',
        'student_name_X',
        'student_name_Y',
        'student_name_font_size',
        'course_title_X',
        'course_title_Y',
        'course_title_font_size',
        'issue_date_X',
        'issue_date_Y',
        'issue_date_font_size',
        'certificate_code_X',
        'certificate_code_Y',
        'certificate_code_font_size',
        'font_color',
        'font_family',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
