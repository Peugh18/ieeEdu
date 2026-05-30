# Despliegue (Deploy) de IEE

Pasos mínimos recomendados para desplegar la aplicación en producción.

## 1. Instalación de dependencias
Asegúrese de instalar los paquetes de Composer y NPM:
```bash
composer install --optimize-autoloader --no-dev
npm install
```

## 2. Construcción de activos Frontend
Para la versión en producción, compilar Vite:
```bash
npm run build
```

## 3. Configuración del entorno
Configurar las variables de entorno para producción en `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

IIE_WHATSAPP_SALES=...
IIE_WHATSAPP_SUPPORT=...
IIE_PLAN_TRIMESTRAL_PRICE=350
IIE_PLAN_SEMESTRAL_PRICE=600
IIE_PLAN_ANUAL_PRICE=990
```

## 4. Correo y colas (obligatorio para notificaciones)
Configurar el servicio de correo y el driver de colas en `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="Instituto IEE"

QUEUE_CONNECTION=database  # o redis
```

En producción, mantener un worker de colas activo:
```bash
php artisan queue:work --tries=3
```

## 5. Base de Datos y Caché
Ejecutar migraciones de manera segura y limpiar / optimizar cachés de Laravel:
```bash
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan view:cache
php artisan event:cache
```

## 6. Almacenamiento
En caso de requerir acceso a archivos públicos, crear el enlace simbólico:
```bash
php artisan storage:link
```

## 7. Scheduler (suscripciones expiradas)
El comando `subscriptions:sync-expired` revoca acceso cuando vence una membresía. En producción, agregar al crontab del servidor:
```bash
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Verificar manualmente:
```bash
php artisan subscriptions:sync-expired
```

## 8. Smoke test post-deploy
- [ ] Login admin y estudiante
- [ ] `/planes` — solicitud de membresía
- [ ] Aprobar pago curso → acceso aula
- [ ] `php artisan test --compact` en CI o staging
- [ ] Verificar correo de verificación (`MustVerifyEmail`) si MAIL está configurado
