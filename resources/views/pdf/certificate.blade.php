<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado - {{ $user->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            font-family: '{{ $template->font_family ?? 'serif' }}', serif;
            position: relative;
            background-color: white;
            -webkit-print-color-adjust: exact;
        }
        .cert-container {
            width: 297mm;
            height: 210mm;
            position: relative;
            background-image: url('{{ $template_image_base64 }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }
        .text-element {
            position: absolute;
            text-align: center;
            transform: translate(-50%, -50%); /* Accurate centering in Chromium */
            white-space: nowrap;
            color: {{ $template->font_color ?? '#57572A' }};
            line-height: 1.2;
        }
        .student-name {
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @if($is_custom)
        <div class="cert-container">
            <!-- Nombre del Alumno -->
            <div class="text-element student-name" style="
                left: {{ $template->student_name_X * 100 }}%; 
                top: {{ $template->student_name_Y * 100 }}%; 
                font-size: {{ $template->student_name_font_size }}pt;
            ">
                {{ $user->name }}
            </div>

            <!-- Título del Curso -->
            <div class="text-element" style="
                left: {{ $template->course_title_X * 100 }}%; 
                top: {{ $template->course_title_Y * 100 }}%; 
                font-size: {{ $template->course_title_font_size }}pt;
            ">
                {{ $course->title }}
            </div>

            <!-- Fecha -->
            <div class="text-element" style="
                left: {{ $template->issue_date_X * 100 }}%; 
                top: {{ $template->issue_date_Y * 100 }}%; 
                font-size: {{ $template->issue_date_font_size }}pt;
            ">
                Emitido el {{ $date }}
            </div>

            <!-- Código de Verificación -->
            <div class="text-element" style="
                left: {{ $template->certificate_code_X * 100 }}%; 
                top: {{ $template->certificate_code_Y * 100 }}%; 
                font-size: {{ $template->certificate_code_font_size }}pt;
                opacity: 0.8;
            ">
                ID: {{ $code }}
            </div>
        </div>
    @else
        <div style="padding: 50px; text-align: center; border: 10px solid {{ $template->font_color ?? '#57572A' }}; height: 100%;">
             <h1 style="font-size: 48pt;">CERTIFICADO</h1>
             <p style="margin-top: 50px;">Se otorga a:</p>
             <h2 style="font-size: 36pt; color: {{ $template->font_color ?? '#57572A' }}">{{ $user->name }}</h2>
             <p style="margin-top: 30px;">Por completar el curso:</p>
             <h3>{{ $course->title }}</h3>
             <footer style="margin-top: 100px;">
                ID: {{ $code }} | Fecha: {{ $date }}
             </footer>
        </div>
    @endif
</body>
</html>
