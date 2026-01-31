# Gema8 - Audio TTS con ElevenLabs

## Background and Motivation
El usuario necesita escuchar las traducciones porque con algunos idiomas (japonés, chino, árabe, etc.) solo mostrar texto no es suficiente. Implementaremos Text-to-Speech usando ElevenLabs API.

## Key Challenges and Analysis
- ElevenLabs tiene voces multilingües de alta calidad
- Necesitamos mapear idiomas de Gema8 a códigos BCP-47 de ElevenLabs
- El audio se genera bajo demanda (no cacheamos para ahorrar storage)
- Añadir coste de créditos para TTS (1 crédito por reproducción)

## High-level Task Breakdown
- [x] Tarea 1: Añadir API key ElevenLabs en config.php
- [x] Tarea 2: Crear includes/elevenlabs.php para llamar a la API
- [x] Tarea 3: Añadir endpoint /api/tts en ApiController
- [x] Tarea 4: Añadir botones de audio en dashboard (traducciones)
- [x] Tarea 5: Añadir botones de audio en whispers (frases)
- [x] Tarea 6: Crear módulo JS reutilizable para TTS

## Project Status Board
- [x] Config: API key añadida + CREDIT_COST_TTS
- [x] Backend: includes/elevenlabs.php + endpoint /api/tts
- [x] Frontend: Botones de audio en dashboard y whispers
- [ ] Testing: Verificar con varios idiomas

---

## Plan Anterior (Completado): Token de Sesión Persistente + Panel Admin

<details>
<summary>Ver plan anterior</summary>

### Background and Motivation
El usuario necesitaba
1. **Token de sesión persistente (60 días)** - Implementar "Remember me" con cookie segura
2. **Panel completo de gestión de usuarios para superadmin (Oracle)** - Listar usuarios, editar créditos, cambiar roles, ver estadísticas

## Key Challenges and Analysis
- La sesión actual usa `SESSION_LIFETIME` de 7 días (cookies de sesión PHP)
- Para 60 días necesitamos un token persistente almacenado en BD (más seguro que solo cookie)
- El rol `Oracle` ya existe y tiene privilegios especiales
- Necesitamos crear: tabla `remember_tokens`, AdminController, vistas del panel

## High-level Task Breakdown

### Tarea 1: Token de sesión persistente (60 días)
- [x] Crear migración para tabla `remember_tokens`
- [x] Modificar `includes/auth.php` para manejar tokens de "remember me"
- [x] Modificar `AuthController.php` para procesar checkbox "remember me"
- [x] Modificar vista de login para añadir checkbox
- [x] Modificar logout para limpiar token

### Tarea 2: Panel de Administración (Oracle)
- [x] Crear `AdminController.php`
- [x] Añadir métodos al modelo `User.php` para listar usuarios
- [x] Añadir métodos al modelo `Profile.php` para setear créditos directamente
- [x] Crear vistas: `views/admin/index.php`, `views/admin/user-edit.php`
- [x] Añadir rutas en `public/index.php`
- [x] Añadir middleware `requireOracle()` para proteger rutas

## Project Status Board
- [x] Tarea 1.1: Crear tabla `remember_tokens` en schema.sql
- [x] Tarea 1.2: Implementar funciones de token en auth.php
- [x] Tarea 1.3: Modificar AuthController para "remember me"
- [x] Tarea 1.4: Actualizar vista de login
- [x] Tarea 2.1: Crear AdminController
- [x] Tarea 2.2: Añadir métodos a modelos User/Profile
- [x] Tarea 2.3: Crear vistas del panel admin
- [x] Tarea 2.4: Añadir rutas y protección

## Executor's Feedback or Assistance Requests
**IMPLEMENTACIÓN COMPLETA** - Pendiente verificación del usuario.

### Acción requerida del usuario:
Ejecutar la migración SQL en la base de datos:
```sql
CREATE TABLE IF NOT EXISTS `remember_tokens` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_token_hash` (`token_hash`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Lessons
- El rol Oracle ya existía en el sistema como superadmin con créditos ilimitados
- La sesión se manejaba con cookies PHP estándar (7 días), ahora añadido token persistente de 60 días
- Se rota el token en cada login automático para mayor seguridad
