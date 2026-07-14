<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Recibida - IEE</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #111827; font-size: 24px; }
        .content { margin-bottom: 20px; line-height: 1.6; }
        .content p { margin: 10px 0; }
        .footer { margin-top: 30px; font-size: 12px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Gracias por contactarnos!</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $consultancyRequest->name }}</strong>,</p>
            <p>Hemos recibido correctamente tu solicitud de consultoría para el área de <strong>{{ $consultancyRequest->area }}</strong>.</p>
            <p>Nuestro equipo de expertos está revisando tus requerimientos y nos pondremos en contacto contigo en un plazo máximo de <strong>24 horas hábiles</strong> para coordinar los siguientes pasos y ofrecerte la mejor solución a medida.</p>
            <p>Si tienes información adicional que consideres importante, puedes responder directamente a este correo.</p>
            <p>Atentamente,<br><strong>El equipo del Instituto de Economía y Empresa</strong></p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Instituto de Economía y Empresa. Todos los derechos reservados.</p>
            <p>Trujillo, La Libertad — Perú</p>
        </div>
    </div>
</body>
</html>
