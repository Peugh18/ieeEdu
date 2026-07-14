<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado - {{ $user->name }}</title>
    @php
        $font = 'times';
        if (($template->font_family ?? '') === 'sans-serif') {
            $font = 'helvetica';
        } elseif (($template->font_family ?? '') === 'cursive') {
            $font = 'times'; // Fallback to times since DomPDF has no native elegant cursive font
        }
    @endphp
    <style>
        @page {
            size: A4 landscape;
            margin: 0px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            margin: 0px !important;
            padding: 0px !important;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: white;
        }
        body {
            font-family: {{ $font }}, serif;
            position: relative;
        }
        .cert-background {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: -10;
        }
        .cert-container {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0px;
            left: 0px;
            z-index: 1;
        }
        .text-element-wrapper {
            position: absolute;
            width: 100%;
            left: 0px;
            text-align: center;
            color: {{ $template->font_color ?? '#57572A' }};
            line-height: 1.2;
            white-space: nowrap;
        }
        .text-element-inner {
            position: relative;
            display: inline-block;
        }
        .student-name {
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    @if($is_custom)
        <img class="cert-background" src="{{ $template_image_base64 }}" alt="Background">
        <div class="cert-container">
            <!-- Nombre del Alumno -->
            <div class="text-element-wrapper" style="
                top: {{ $template->student_name_Y * 100 }}%; 
                font-size: {{ $template->student_name_font_size }}pt;
                margin-top: -{{ $template->student_name_font_size * 0.6 }}pt;
            ">
                <span class="text-element-inner student-name" style="left: {{ ($template->student_name_X * 100) - 50 }}%;">
                    {{ $user->name }}
                </span>
            </div>

            <!-- Título del Curso -->
            <div class="text-element-wrapper" style="
                top: {{ $template->course_title_Y * 100 }}%; 
                font-size: {{ $template->course_title_font_size }}pt;
                margin-top: -{{ $template->course_title_font_size * 0.6 }}pt;
            ">
                <span class="text-element-inner" style="left: {{ ($template->course_title_X * 100) - 50 }}%;">
                    {{ $course->title }}
                </span>
            </div>

            <!-- Fecha -->
            <div class="text-element-wrapper" style="
                top: {{ $template->issue_date_Y * 100 }}%; 
                font-size: {{ $template->issue_date_font_size }}pt;
                margin-top: -{{ $template->issue_date_font_size * 0.6 }}pt;
            ">
                <span class="text-element-inner" style="left: {{ ($template->issue_date_X * 100) - 50 }}%;">
                    Emitido el {{ $date }}
                </span>
            </div>

            <!-- Código de Verificación -->
            <div class="text-element-wrapper" style="
                top: {{ $template->certificate_code_Y * 100 }}%; 
                font-size: {{ $template->certificate_code_font_size }}pt;
                margin-top: -{{ $template->certificate_code_font_size * 0.6 }}pt;
                opacity: 0.8;
            ">
                <span class="text-element-inner" style="left: {{ ($template->certificate_code_X * 100) - 50 }}%;">
                    ID: {{ $code }}
                </span>
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
