# Gema∞ — App Review & Improvement Suggestions

## Background and Motivation
El usuario solicita una revisión completa de la app Gema∞ (language learning con AI) enfocada en **interfaz, usabilidad y funcionalidades**. La app ya está funcional con: traductor bidireccional, daily tips, whispers (frases situacionales), language doubts, historial (echoes), sistema de créditos, auth, TTS (ElevenLabs), y panel admin.

---

## Key Challenges and Analysis

### A. INTERFAZ (UI)

#### A1. Tailwind CDN → Build de producción ⚠️ RENDIMIENTO
- Se usa `<script src="https://cdn.tailwindcss.com">` que es solo para desarrollo. En producción genera FOUC (flash of unstyled content) y carga ~300KB de JS innecesario.
- **Sugerencia**: Compilar Tailwind a CSS estático con `npx tailwindcss -o style.css --minify`. El CSS resultante será ~10-15KB.

#### A2. Lucide Icons desde unpkg sin versión fija ⚠️
- `<script src="https://unpkg.com/lucide@latest">` puede romperse si hay breaking changes.
- **Sugerencia**: Fijar versión, ej: `lucide@0.344.0`.

#### A3. Dark Mode 🌙
- No hay soporte de dark mode. Es muy esperado en apps modernas, especialmente language learning que se usa de noche.
- **Sugerencia**: Añadir toggle dark/light con `prefers-color-scheme` como default y persistencia en `localStorage`.

#### A4. Skeleton loaders en lugar de spinners
- Cuando se carga el Daily Tip o se espera traducción, solo hay un spinner genérico.
- **Sugerencia**: Usar skeleton placeholders (pulsing bars) para dar mejor sensación de carga progresiva.

#### A5. Empty states más atractivos
- Los empty states de Echoes y Whispers son correctos pero básicos.
- **Sugerencia**: Añadir ilustraciones SVG ligeras o animaciones Lottie pequeñas para hacer la experiencia más amigable.

#### A6. Consistencia visual en botones inline style
- Varios botones usan `style="background: linear-gradient(...)"` inline en lugar de clases CSS reutilizables (ej: whisperBtn, askBtn).
- **Sugerencia**: Crear clases `.btn-emerald`, `.btn-violet` en el `<style>` global para mantener consistencia.

---

### B. USABILIDAD (UX)

#### B1. Atajos de teclado ⌨️
- No hay soporte para Ctrl+Enter/Cmd+Enter para enviar traducción o pregunta.
- **Sugerencia**: Añadir `keydown` listener en los textareas principales para submit rápido.

#### B2. updateCredits() no actualiza el badge en tiempo real 🔴 BUG
- La función `updateCredits()` en dashboard hace un fetch GET a `/` pero NO actualiza el DOM.
- El badge de créditos no refleja el gasto hasta que el usuario recarga la página.
- **Sugerencia**: Crear endpoint `/api/credits` que devuelva `{credits: N}` y actualizar `#creditsCount` directamente.

#### B3. Cambio de idioma recarga toda la página
- `changeLanguage()` crea un form invisible y hace submit completo.
- **Sugerencia**: Hacerlo vía AJAX y actualizar solo los placeholders/labels dinámicamente sin reload.

#### B4. No hay confirmación visual del modo Ephemeral
- El toggle ephemeral cambia estilos sutiles, pero no hay tooltip explicando qué hace.
- **Sugerencia**: Añadir tooltip/popover al hover que explique: "Ephemeral translations won't be saved to your history".

#### B5. Textarea auto-resize
- Los textareas tienen `min-h-28` fijo. Si escribes mucho texto, necesitas scroll interno.
- **Sugerencia**: Implementar auto-resize con `textarea.style.height = textarea.scrollHeight + 'px'` en `input` event.

#### B6. No hay búsqueda en Echoes/Whispers 🔍
- Si el usuario tiene muchas traducciones, no puede buscar.
- **Sugerencia**: Añadir campo de búsqueda por texto en `/history` y `/whispers` (filtro client-side o server-side).

#### B7. No hay paginación
- Las listas de traducciones y whispers cargan TODO de una vez.
- **Sugerencia**: Añadir paginación o infinite scroll (especialmente importante si un usuario tiene 500+ traducciones).

#### B8. Botón de copiar traducción 📋
- No hay forma de copiar la traducción al portapapeles con un click.
- **Sugerencia**: Añadir botón "Copy" junto al botón TTS en el resultado de traducción.

#### B9. Desktop nav no tiene indicador de página activa
- En mobile la nav marca `active`, pero en desktop los botones Echoes/Whispers no indican la página actual.
- **Sugerencia**: Aplicar clase activa a los botones desktop del header cuando estás en esa ruta.

#### B10. Traducir con Enter en lugar de click
- El flujo principal (traducir) requiere click en "Echo It".
- **Sugerencia**: Permitir Ctrl+Enter desde el textarea para enviar.

---

### C. FUNCIONALIDADES

#### C1. Favoritos / Bookmarks ⭐
- No hay forma de marcar traducciones como favoritas para repaso.
- **Sugerencia**: Añadir campo `is_favorite` en `translations` y filtro en historial.

#### C2. Exportar datos 📥
- No hay exportación de traducciones o whispers.
- **Sugerencia**: Botón para exportar historial como CSV o PDF.

#### C3. Flashcards / Modo repaso 🧠
- La app almacena traducciones con contador de repetición pero no ofrece un modo de repaso activo.
- **Sugerencia**: Vista de flashcards (SRS-like) usando las traducciones guardadas. Mostrar original → adivinar traducción → revelar.

#### C4. Compartir whispers 🔗
- No hay forma de compartir una colección de frases.
- **Sugerencia**: Generar enlace público temporal para compartir un whisper set.

#### C5. Historial de preguntas (Language Doubts)
- Las respuestas de "Language Doubts" no se guardan en ningún sitio.
- **Sugerencia**: Guardar en una tabla `questions` y mostrar en una sección del perfil/historial.

#### C6. Notificaciones de créditos bajos ⚠️
- No hay aviso cuando los créditos están bajos.
- **Sugerencia**: Mostrar warning visual cuando credits < 10, y desactivar acciones cuando credits = 0 (con mensaje claro).

#### C7. Rate limiting visual
- Si la API de Gemini o ElevenLabs falla, el error es genérico.
- **Sugerencia**: Mensajes de error más descriptivos y retry automático con backoff.

#### C8. Soporte multi-idioma en la UI 🌐
- Toda la interfaz está en inglés.
- **Sugerencia**: i18n básico para la UI (al menos español + inglés), dado que es una app de idiomas.

#### C9. Cambio de contraseña
- No hay opción de cambiar contraseña desde la cuenta (solo forgot password).
- **Sugerencia**: Añadir sección "Change Password" en Account con old_password + new_password.

#### C10. Pronunciación interactiva en Whispers
- Las frases de whispers muestran pronunciación como texto, pero podrían ser más interactivas.
- **Sugerencia**: Colorear sílabas acentuadas o añadir tooltip de IPA.

---

### D. TÉCNICO / SEGURIDAD (bonus)

#### D1. `formatMarkdown()` JS es vulnerable a XSS
- El regex `formatMarkdown()` aplica `.replace()` sobre texto que puede contener HTML inyectado desde la respuesta de Gemini.
- **Sugerencia**: Sanitizar el texto con `escapeHtml()` ANTES de aplicar formatMarkdown.

#### D2. CSRF token expuesto en variable global JS
- `const csrfToken = '<?= csrfToken() ?>'` es visible en el source. Esto es normal para SPAs pero podría mejorarse.
- No es un problema real ya que CSRF tokens son per-session, pero se podría usar un meta tag en su lugar.

#### D3. Service Worker cache
- `sw.js` existe pero no vi estrategia de cache para assets estáticos.
- **Sugerencia**: Cachear fonts, iconos, y CSS/JS para mejor experiencia offline/PWA.

---

## High-level Task Breakdown (Priorizado)

### 🔴 Alta prioridad (bugs + quick wins)
1. [ ] Fix `updateCredits()` para actualizar badge en tiempo real (B2)
2. [ ] Atajo Ctrl+Enter para traducir/preguntar (B1/B10)
3. [ ] Fijar versión de Lucide icons (A2)
4. [ ] Botón copiar traducción (B8)
5. [ ] Fix XSS potencial en formatMarkdown (D1)

### 🟡 Media prioridad (UX improvements)
6. [ ] Textarea auto-resize (B5)
7. [ ] Tooltip para Ephemeral mode (B4)
8. [ ] Búsqueda en Echoes y Whispers (B6)
9. [ ] Paginación en listas (B7)
10. [ ] Clases CSS reutilizables para botones coloreados (A6)
11. [ ] Desktop nav con indicador activo (B9)
12. [ ] Cambio de contraseña en Account (C9)
13. [ ] Warning de créditos bajos (C6)

### 🟢 Baja prioridad (features nuevas)
14. [ ] Dark mode (A3)
15. [ ] Favoritos en traducciones (C1)
16. [ ] Guardar historial de Language Doubts (C5)
17. [ ] Flashcards / modo repaso (C3)
18. [ ] Exportar datos CSV (C2)
19. [ ] Skeleton loaders (A4)
20. [ ] i18n de la UI (C8)
21. [ ] Tailwind build de producción (A1)
22. [ ] Cambio de idioma sin reload (B3)

---

## FEATURE: Conversaciones en tiempo real para viajeros

### Concepto
Chat bidireccional con traducción contextual. El viajero abre una conversación (título = nombre de persona), escribe lo que dice él o lo que le dicen, y Gemini traduce con contexto completo de la conversación.

### Decisiones de diseño
- **Mobile-first**: UN solo textarea + toggle dirección (Yo digo / Me dicen)
- **Burbujas tipo chat**: derecha (azul) = yo, izquierda (gris) = ellos. Ambas muestran original + traducción
- **Contexto a Gemini**: últimos 15-20 mensajes + resumen automático de anteriores
- **Settings por conversación**: Level (Principiante/Medio/Avanzado), Tone (Mantener/Formal/Casual/Gracioso), Fidelity (Literal/Natural/Libre)
- **Copy + TTS** por mensaje
- **Nota cultural** opcional cuando Gemini detecte algo relevante
- **Archivar** conversaciones (no borrar)
- **NO** sugerencias de respuesta rápida
- **Coste**: 1 crédito por mensaje

### Modelo de datos
```sql
conversations: id, user_id, title, target_language, level, tone, fidelity, summary, is_archived, created_at, updated_at
conversation_messages: id, conversation_id, direction (me/them), original_text, translated_text, cultural_note, created_at
```

### Task Breakdown
1. [ ] SQL schema: conversations + conversation_messages
2. [ ] Models: Conversation.php + ConversationMessage.php
3. [ ] Gemini: método de traducción contextual
4. [ ] ConversationController.php (list, view, create, archive, delete)
5. [ ] API endpoints: send message, delete message
6. [ ] Routes en index.php
7. [ ] Vista: lista de conversaciones (búsqueda, orden fecha, crear)
8. [ ] Vista: chat individual (mobile-first, burbujas, copy/TTS)
9. [ ] Navegación: añadir a header + mobile nav
10. [ ] Settings: level/tone/fidelity por conversación

## Project Status Board
- [x] Review completo de la app (22 sugerencias documentadas)
- [ ] Feature Conversaciones — EN PROGRESO

## Executor's Feedback or Assistance Requests
Ninguno por ahora.

## Lessons
- El rol Oracle ya existía en el sistema como superadmin con créditos ilimitados
- La sesión se manejaba con cookies PHP estándar (7 días), ahora añadido token persistente de 60 días
- Se rota el token en cada login automático para mayor seguridad
- La app usa Tailwind CDN (solo dev), Lucide icons sin versión fija, y vanilla JS
- El backend es PHP puro sin framework, con MVC manual y Gemini API + ElevenLabs TTS

---

## Plan Anterior (Completado): Audio TTS con ElevenLabs

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
- [x] Testing: Verificado y funcionando

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
