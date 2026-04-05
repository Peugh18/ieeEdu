<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificado de Finalización</title>
    <style>
        @page {
            margin: 0;
            size: landscape;
        }
        body {
            font-family: 'Serif', 'Times New Roman', Times, serif;
            background-color: #fafaf5;
            margin: 0;
            color: #333;
        }
        .certificate-container {
            width: 100%;
            height: 100vh;
            border: 40px solid #57572a;
            position: relative;
            background-image: radial-gradient(circle at top right, rgba(212, 175, 55, 0.05), transparent),
                              radial-gradient(circle at bottom left, rgba(87, 87, 42, 0.05), transparent);
        }
        .inner-border {
            position: absolute;
            top: 20px; left: 20px; right: 20px; bottom: 20px;
            border: 2px solid #d4af37;
        }
        .content {
            padding: 80px 100px;
            text-align: center;
        }
        .u-logo {
            width: 160px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 55px;
            text-transform: uppercase;
            letter-spacing: 12px;
            color: #57572a;
            margin-bottom: 0px;
            font-weight: bold;
        }
        .subtitle {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #d4af37;
            margin-bottom: 50px;
        }
        .recognition {
            font-style: italic;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .student-name {
            font-size: 48px;
            font-weight: bold;
            color: #1a1a1a;
            border-bottom: 2px solid #57572a;
            display: inline-block;
            margin-bottom: 30px;
            padding: 0 40px;
        }
        .course-text {
            font-size: 22px;
            line-height: 1.6;
            margin-bottom: 60px;
        }
        .course-name {
            font-weight: bold;
            color: #57572a;
        }
        .footer {
            position: absolute;
            bottom: 100px;
            width: 80%;
            left: 10%;
            display: table;
        }
        .footer-item {
            display: table-cell;
            width: 33.33%;
            vertical-align: bottom;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #57572a;
            width: 180px;
            margin: 0 auto 10px;
        }
        .signature-text {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #888;
        }
        .date {
            font-size: 14px;
            font-style: italic;
        }
        .certificate-id {
            position: absolute;
            bottom: 40px;
            right: 40px;
            font-size: 10px;
            color: #bfa34b;
            letter-spacing: 1px;
        }
        .seal {
             width: 100px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="inner-border"></div>
        <div class="content">
            <h1 class="title">DIPLOMA</h1>
            <p class="subtitle">Instituto de Economía y Empresa</p>
            
            <p class="recognition">Se otorga con orgullo el presente reconocimiento a:</p>
            <div class="student-name">{{ $user_name }}</div>
            
            <p class="course-text">
                Por haber completado satisfactoriamente los requisitos académicos del curso: <br>
                <span class="course-name">{{ $course_name }}</span>
            </p>

            <div class="footer">
                <div class="footer-item">
                    <p class="date">Emitido el {{ $date }}</p>
                </div>
                <div class="footer-item">
                    <div style="margin-bottom: -15px;">
                        <img src="{{ public_path('images/signature.png') }}" style="width: 120px; height: auto; opacity: 0.8;" />
                    </div>
                    <div class="signature-line"></div>
                    <p class="signature-text">Sello de Autenticidad</p>
                </div>
                <div class="footer-item">
                    <div style="margin-bottom: -15px;">
                        <img src="{{ public_path('images/signature.png') }}" style="width: 120px; height: auto; transform: scaleX(-1); opacity: 0.8;" />
                    </div>
                    <div class="signature-line"></div>
                    <p class="signature-text">Dirección Académica</p>
                </div>
            </div>
            
            <div class="certificate-id">Código de Verificación: {{ $code }}</div>
        </div>
    </div>
</body>
</html>
