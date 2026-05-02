# GUIA DE TESTING - Fixes Implementados

> Fecha: 2026-05-01

---

## 1. RUTA API `/api/leads/whatsapp` (CRITICO)

**Archivo modificado:** `routes/web.php`

**Que hace:** Registra la ruta POST que el frontend `useCart.ts` intenta llamar al enviar un pedido por WhatsApp.

**Como testear:**
```bash
# 1. Logueate como usuario normal
# 2. Agrega un curso al carrito
# 3. Abre DevTools > Network
# 4. Click "Enviar por WhatsApp"
# 5. Debe aparecer POST /api/leads/whatsapp con status 200
# 6. Verificar en la tabla `whatsapp_leads` que se guardó el registro
```

---

## 2. RATE LIMITING EN CONSULTORIA (CRITICO)

**Archivo modificado:** `routes/web.php`

**Que hace:** Limita a 5 solicitudes por minuto en el formulario de consultoria.

**Como testear:**
```bash
# 1. Ve a /consultoria sin loguearte
# 2. Envia el formulario 6 veces rapido
# 3. La 6ta debe devolver 429 Too Many Requests
```

---

## 3. AUTORIZACION EN COMENTARIOS (CRITICO)

**Archivo modificado:** `app/Http/Controllers/Student/LessonCommentController.php`

**Que hace:** Verifica que el usuario tenga acceso al curso antes de comentar en una leccion.

**Como testear:**
```bash
# 1. Logueate como usuario A (matriculado en Curso X)
# 2. Intenta POST /student/comments/{lesson_del_curso_Y} (sin matricula)
# 3. Debe devolver 403 Forbidden
# 4. El comentario NO debe guardarse en la BD
```

---

## 4. NULLABLE USER EN WHATSAPP LEADS (CRITICO)

**Archivo modificado:** `app/Http/Controllers/WhatsappLeadController.php`

**Que hace:** Evita crash si se llama la ruta sin usuario autenticado.

**Como testear:**
```bash
# 1. Deslogueate
# 2. Intenta POST /api/leads/whatsapp con payload valido
# 3. No debe dar error 500 - debe guardar user_id = null
```

---

## 5. N+1 QUERIES EN ADMIN DASHBOARD (CRITICO)

**Archivo modificado:** `app/Http/Controllers/Admin/DashboardController.php`

**Que hace:** Reduce de ~30 queries a ~4 queries agrupadas para los charts.

**Como testear:**
```bash
# 1. Logueate como admin
# 2. Ve a /admin/dashboard
# 3. En Laravel Telescope o log, verificar que solo hay:
#    - 1 query por chart (weekly, monthly, quarterly)
#    - Con GROUP BY en lugar de multiples SUM individuales
# 4. El dashboard debe cargar mucho mas rapido con muchos pagos
```

---

## 6. CARGA MASIVA DE ATTEMPTS (CRITICO)

**Archivo modificado:** `app/Http/Controllers/Admin/CourseController.php`

**Que hace:** Quita `'quizzes.attempts.user'` del eager loading en el edit de curso.

**Como testear:**
```bash
# 1. Logueate como admin
# 2. Ve a editar un curso con muchos estudiantes (ej. 500+)
# 3. La pagina debe cargar sin timeout
# 4. En Telescope, verificar que no se carga la tabla quiz_attempts
```

---

## 7. PAGINACION EN EXPLORE (CRITICO)

**Archivo modificado:** `app/Http/Controllers/Student/DashboardController.php`

**Que hace:** `Book::all()` y `Article::all()` ahora usan `paginate(12)`.

**Como testear:**
```bash
# 1. Logueate como estudiante
# 2. Ve a /student/explore/publications
# 3. Verificar que aparece paginacion (solo 12 items por pagina)
# 4. El tiempo de carga debe ser rapido aunque haya 1000 libros
```

---

## 8. LIMPIEZA SETINTERVAL EN CLASSROOM (MEDIO)

**Archivo modificado:** `resources/js/pages/student/Classroom.vue`

**Que hace:** Limpia el interval de `now` cuando el componente se destruye.

**Como testear:**
```javascript
// 1. Abre el classroom de un curso
// 2. Navega a otra pagina (ej. Dashboard)
// 3. En DevTools > Performance > Memory:
//    - Tomar snapshot antes y despues
//    - No debe haber leaks de Classroom.vue
```

---

## 9. TIPOS CENTRALIZADOS (MEDIO)

**Archivo creado:** `resources/js/types/course.ts`

**Que hace:** Centraliza todas las interfaces de TypeScript.

**Como testear:**
```bash
# 1. Correr: npm run build
# 2. No debe haber errores de TypeScript
# 3. (Opcional) Refactorizar Cursos.vue, CourseCard.vue para importar desde course.ts
```

---

## 10. IMPORT STR EN COURSECONTROLLER (MEDIO)

**Archivo modificado:** `app/Http/Controllers/Admin/CourseController.php`

**Que hace:** Agrega `use Illuminate\Support\Str;` en lugar de facade global.

**Como testear:**
```bash
# 1. Logueate como admin
# 2. Ve a un curso > Duplicar
# 3. El curso duplicado debe crearse correctamente con slug unico
```

---

## 11. CACHE EN DASHBOARDS (MEJORA)

**Archivos modificados:** `Admin/DashboardController.php`, `Student/DashboardController.php`

**Que hace:** Cachea stats del admin por 5 min y del estudiante por 1 min.

**Como testear:**
```bash
# 1. Ve al admin dashboard
# 2. Recarga la pagina varias veces
# 3. En Telescope, la 2da+ recarga no debe ejecutar queries de stats
# 4. Esperar 5 minutos y recargar - ahora SI debe ejecutar queries
```

---

## 12. SELECT ESPECIFICOS EN PUBLICCOURSECONTROLLER (MEJORA)

**Archivo modificado:** `app/Http/Controllers/PublicCourseController.php`

**Que hace:** Solo selecciona columnas necesarias en lugar de `*`.

**Como testear:**
```bash
# 1. Ve a la pagina de inicio /
# 2. En Telescope, verificar que el query de courses tiene SELECT explicito
# 3. El payload JSON de Inertia debe ser mas pequeno
```

---

## Comando general para verificar todos los cambios

```bash
# Verificar sintaxis PHP
php artisan route:list

# Verificar que no hay errores de compilacion
npm run build

# Limpiar cache (despues de deploy)
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```
