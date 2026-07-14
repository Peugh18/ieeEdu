<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Consultoría</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #111827; font-size: 24px; }
        .details { margin-bottom: 20px; }
        .details p { margin: 10px 0; line-height: 1.5; }
        .details strong { color: #374151; }
        .message-box { background-color: #f9fafb; border-left: 4px solid #3b82f6; padding: 15px; margin-top: 20px; border-radius: 4px; }
        .footer { margin-top: 30px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Solicitud de Consultoría</h1>
            <p>Se ha recibido una nueva solicitud desde la página web.</p>
        </div>
        
        <div class="details">
            <p><strong>Nombre:</strong> {{ $consultancyRequest->name }}</p>
            <p><strong>Email:</strong> <a href="mailto:{{ $consultancyRequest->email }}">{{ $consultancyRequest->email }}</a></p>
            <p><strong>Teléfono / WhatsApp:</strong> {{ $consultancyRequest->phone ?? 'No proporcionado' }}</p>
            <p><strong>Empresa / Institución:</strong> {{ $consultancyRequest->company ?? 'No proporcionada' }}</p>
            <p><strong>Área de interés:</strong> {{ $consultancyRequest->area }}</p>
            
            <div class="message-box">
                <p><strong>Mensaje del cliente:</strong></p>
                <p>{{ $consultancyRequest->message }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Este es un correo automático generado por el sistema de iieEdu.</p>
        </div>
    </div>
</body>
</html>
