<<<<<<< /Users/dvdgp/Documents/Codeapps/gema8 PHP/.cursor/scratchpad.md
# Gema8 - Funcionalidad de Voz/Pronunciación

## Background and Motivation
El usuario está aprendiendo francés, un idioma donde la pronunciación es crítica (liaisons, nasales, ritmo). Necesita escuchar las traducciones y frases para mejorar su aprendizaje auditivo y pronunciación.

## Key Challenges and Analysis

### Opciones Técnicas Evaluadas:

1. **Web Speech API (SpeechSynthesis) - ✅ RECOMENDADA**
   - Gratuita, nativa del navegador
   - No requiere API keys ni configuración
   - Soporta francés (`fr-FR`)
   - Funciona offline
   - Implementación: ~2 horas
   - Limitación: Calidad de voz depende del OS/navegador

2. **Google Cloud Text-to-Speech**
   - Alta calidad (Neural2, WaveNet)
   - Requiere API key y cobra por uso (~$4/millon chars)
   - Mayor complejidad de backend
   - Costos adicionales para el usuario

3. **OpenAI TTS**
   - Buena calidad
   - $0.015 por 1K caracteres
   - Requiere API key adicional
=======
# Gema8 - TTS de Alta Calidad (Reemplazo Web Speech API)

## Background and Motivation
La voz nativa del navegador (Web Speech API) no tiene calidad suficiente para aprender francés correctamente. Se necesita TTS de alta fidelidad que reproduzca bien las nasales, liaisons y ritmo del francés.

## Key Challenges and Analysis

### Opciones Evaluadas:

| Opción | Calidad | Costo | Setup | Recomendación |
|--------|---------|-------|-------|---------------|
| **ElevenLabs** | ⭐⭐⭐ Excelente | Gratis: 10K chars/mes | API Key | ✅ **Mejor opción** |
| **OpenAI TTS** | ⭐⭐⭐ Muy buena | $0.015/1K chars | API Key | Buena alternativa |
| **Google Cloud TTS** | ⭐⭐⭐ Muy buena | ~$4/millón chars | API Key + Billing | Más complejo |

### Decisión Recomendada: **ElevenLabs**
- **Gratis**: 10,000 caracteres/mes (suficiente para pruebas)
- **Calidad**: Mejor del mercado para idiomas europeos
- **Francés**: Voz "Liam" o "Matilda" excelentes para francés
- **Latencia**: ~1-2 segundos
- **Setup**: Solo API key, sin billing obligatorio inicial

## Requisitos para Implementar

### Opción A: ElevenLabs (Recomendada)
1. **API Key gratuita**: Registrarse en elevenlabs.io → API Keys → Create
2. **Sin costo inicial**: 10K caracteres/mes gratis
3. **Voces francesas recomendadas**: "Liam", "Matilda", "Jessica"

### Opción B: OpenAI TTS
1. **API Key de OpenAI**: Requiere saldo ($5 mínimo)
2. **Modelo**: `tts-1` o `tts-1-hd`
3. **Voz**: `alloy`, `echo`, `fable` (ninguna específica francesa, pero calidad buena)

### Opción C: Google Cloud TTS (Overkill)
1. **Cuenta GCP** + proyecto
2. **Billing** obligatorio
3. **API Key** separada

## Plan de Implementación ElevenLabs

### Tarea 1: Backend - Endpoint TTS
- [ ] Crear `POST /api/tts` en `ApiController.php`
- [ ] Llamada cURL a ElevenLabs API
- [ ] Devolver audio como base64 o URL
- [ ] Cachear resultados (misma frase = no recobrar)

### Tarea 2: Frontend - Reemplazar TTS
- [ ] Modificar `public/js/tts.js` para usar endpoint `/api/tts`
- [ ] Reproducir audio desde base64/URL
- [ ] Mantener controles de velocidad (usando playbackRate de Audio)

### Tarea 3: Configuración
- [ ] Añadir `ELEVENLABS_API_KEY` a `config/config.php`
- [ ] Documentar cómo obtener API key

## ¿Qué necesito de ti?

Para proceder, elige una opción:

**Opción 1 (Rápida)**: Dame una API key de ElevenLabs y implemento ahora mismo.
- Ve a https://elevenlabs.io/app/sign-up
- Settings → API Keys → Create API Key
- Me pasas la key (es gratis, 10K chars/mes)

**Opción 2**: Usar OpenAI TTS (si ya tienes saldo en OpenAI)
- Confirmar que tienes API key de OpenAI con saldo

**Opción 3**: Te preparo el código y tú añades tu API key después
- Implemento todo con placeholder `YOUR_ELEVENLABS_API_KEY`
- Tú configuras luego

**¿Cuál prefieres?**
>>>>>>> /Users/dvdgp/.windsurf/worktrees/gema8 PHP/gema8 PHP-83dd6207/.cursor/scratchpad.md

### Decisión:
Implementar **Web Speech API** como solución MVP. Es:
- Inmediata (no config adicional)
- Gratuita para usuarios
- Suficiente para aprendizaje de idiomas
- Fácil de mejorar luego con servicios pagos si se requiere más calidad

## High-level Task Breakdown

### Tarea 1: Crear módulo de Text-to-Speech (Frontend)
**Success Criteria:** Botón de "Escuchar" funciona en traducciones y frases

- [ ] Crear `public/js/tts.js` - Módulo SpeechSynthesis
  - Detectar soporte del navegador
  - Seleccionar voz apropiada por idioma (fr-FR para francés)
  - Función `speak(text, lang)` con cola de reproducción
  - Manejo de estados (playing/paused)
  
- [ ] Añadir helper `getBestVoice(lang)` que seleccione la mejor voz disponible
- [ ] Añadir funciones de control: play, pause, stop
- [ ] Manejar eventos: onstart, onend, onerror

### Tarea 2: Integrar TTS en el Dashboard (Traducciones)
**Success Criteria:** Botón de audio aparece junto a cada traducción

- [ ] Modificar `views/dashboard/index.php`
  - Añadir botón 🔊 junto al texto traducido
  - Pasar idioma target al botón para selección de voz correcta
  - Estilos consistentes con el diseño existente
  
- [ ] Modificar JavaScript del dashboard
  - Conectar botón con módulo TTS
  - Feedback visual durante reproducción (animación)

### Tarea 3: Integrar TTS en Whispers (Frases situacionales)
**Success Criteria:** Cada frase tiene botón de audio individual

- [ ] Modificar `views/dashboard/index.php` - sección whisperResult
  - Añadir botón de audio a cada frase generada
  - Incluir pronunciación fonética si existe
  
- [ ] Modificar `views/whispers/index.php`
  - Añadir botón de audio a cada frase guardada
  - Pasar `target_language` del whisper para voz correcta

### Tarea 4: Soporte multi-idioma de voces
**Success Criteria:** Voces correctas para cada idioma soportado

- [ ] Mapear códigos de idioma Gema8 a códigos BCP-47:
  - `french` → `fr-FR`
  - `spanish` → `es-ES` / `es-MX`
  - `german` → `de-DE`
  - etc.
  
- [ ] Fallback: Si no hay voz específica, usar voz genérica del idioma
- [ ] UI: Mostrar warning si navegador no soporta SpeechSynthesis

### Tarea 5: UX mejorada para aprendizaje
**Success Criteria:** Usuario puede repetir, pausar, ajustar velocidad

- [ ] Añadir control de velocidad (0.5x, 0.75x, 1x, 1.25x)
- [ ] Botón de "repetir" para práctica
- [ ] Indicador visual de progreso mientras habla
- [ ] Atajo de teclado: Espacio para reproducir/pausar

## Project Status Board
<<<<<<< /Users/dvdgp/Documents/Codeapps/gema8 PHP/.cursor/scratchpad.md
<<<<<<< /Users/dvdgp/Documents/Codeapps/gema8 PHP/.cursor/scratchpad.md
<<<<<<< /Users/dvdgp/Documents/Codeapps/gema8 PHP/.cursor/scratchpad.md
<<<<<<< /Users/dvdgp/Documents/Codeapps/gema8 PHP/.cursor/scratchpad.md
- [ ] Tarea 1: Módulo TTS JavaScript
- [ ] Tarea 2: Integración en Dashboard/Traducciones  
- [ ] Tarea 3: Integración en Whispers
- [ ] Tarea 4: Soporte multi-idioma
- [ ] Tarea 5: Mejoras UX (velocidad, repeticiones)
=======
=======
>>>>>>> /Users/dvdgp/.windsurf/worktrees/gema8 PHP/gema8 PHP-83dd6207/.cursor/scratchpad.md
=======
>>>>>>> /Users/dvdgp/.windsurf/worktrees/gema8 PHP/gema8 PHP-83dd6207/.cursor/scratchpad.md
- [x] Tarea 1: Módulo TTS JavaScript (`public/js/tts.js`) - Implementado con soporte multi-idioma
- [x] Tarea 2: Integración en Dashboard/Traducciones - Botón de audio con velocidad ajustable
- [x] Tarea 3: Integración en Whispers - Botón de audio en frases generadas y guardadas
- [x] Tarea 4: Soporte multi-idioma - Mapeo completo de idiomas Gema8 a BCP-47
- [x] Tarea 5: Mejoras UX - Control de velocidad (0.5x - 1.25x) y botón de repetir

**IMPLEMENTACIÓN COMPLETA** - Listo para pruebas
<<<<<<< /Users/dvdgp/Documents/Codeapps/gema8 PHP/.cursor/scratchpad.md
<<<<<<< /Users/dvdgp/Documents/Codeapps/gema8 PHP/.cursor/scratchpad.md
>>>>>>> /Users/dvdgp/.windsurf/worktrees/gema8 PHP/gema8 PHP-83dd6207/.cursor/scratchpad.md
=======
>>>>>>> /Users/dvdgp/.windsurf/worktrees/gema8 PHP/gema8 PHP-83dd6207/.cursor/scratchpad.md
=======
>>>>>>> /Users/dvdgp/.windsurf/worktrees/gema8 PHP/gema8 PHP-83dd6207/.cursor/scratchpad.md
=======
- [x] **ElevenLabs TTS Implementado** - 2025-01-31

### Cambios realizados:
1. **Backend** (`controllers/ApiController.php`):
   - Nuevo endpoint `POST /api/tts`
   - Integración con ElevenLabs API usando modelo `eleven_multilingual_v2`
   - Voz: Adam (multilingüe) - soporta francés, español, alemán, etc.
   - Devuelve audio como base64

2. **Configuración** (`config/config.php`):
   - Añadida constante `ELEVENLABS_API_KEY`

3. **Frontend** (`public/js/tts.js`):
   - Reescrito completamente para usar ElevenLabs vía AJAX
   - Mantiene compatibilidad con controles de velocidad
   - Soporte para repetir frases

4. **Vistas**:
   - `views/dashboard/index.php` - Botones TTS en traducciones
   - `views/whispers/index.php` - Botones TTS en frases guardadas

### Estado: **LISTO PARA PROBAR**

## Executor's Feedback or Assistance Requests
**IMPLEMENTACIÓN COMPLETA** - Pendiente verificación del usuario.

### Para probar:
1. Ve al Dashboard
2. Traduce algo al **francés**
3. Click en 🔊 para escuchar con voz ElevenLabs
4. Prueba velocidad **0.5x** para practicar pronunciación
5. Ve a Whispers y prueba frases guardadas

### Notas:
- **Voz usada**: Adam (multilingüe) - ID: `pNInz6obpgDQGcFmaJgB`
- **Modelo**: `eleven_multilingual_v2` (soporta 28 idiomas)
- **Costo**: 10K caracteres/mes gratis con tu API key
- **Latencia**: ~1-2 segundos (generación en servidor)

Si la voz no te convence, podemos probar otras voces de ElevenLabs como:
- `XB0fDUnXU5powFXDhCwa` - Charlotte (femenina, francés)
- `Xb7hH8MSUJpSbSDYk0k2` - Alice (femenina, multilingüe)
>>>>>>> /Users/dvdgp/.windsurf/worktrees/gema8 PHP/gema8 PHP-83dd6207/.cursor/scratchpad.md

## Executor's Feedback or Assistance Requests
**PLAN CREADO** - Esperando aprobación del usuario para proceder con implementación.

### Consideraciones importantes:
1. **Web Speech API es 100% frontend** - No requiere cambios en backend ni costos adicionales
2. **Soporte del navegador:** Chrome, Edge, Safari, Firefox modernos lo soportan
3. **Calidad de voz:** Depende del sistema operativo (Windows/Mac tienen buenas voces francés)
4. **Tiempo estimado:** 2-3 horas de implementación

## Lessons
- Web Speech API es la solución más práctica para MVP de voz
- Las voces varían por OS: Mac tiene excelentes voces multilingües
- Siempre hay que manejar el caso de navegadores sin soporte
- La velocidad de reproducción ayuda mucho al aprendizaje de idiomas

---

## Plan Anterior (Completado): Token de Sesión Persistente + Panel Admin
*(Preservado para referencia histórica)*

<details>
<summary>Ver plan anterior</summary>

### Background and Motivation
El usuario necesitaba:
1. **Token de sesión persistente (60 días)** - Implementar "Remember me" con cookie segura
2. **Panel completo de gestión de usuarios para superadmin (Oracle)**

### Project Status Board (Anterior - Completado)
- [x] Tarea 1.1: Crear tabla `remember_tokens` en schema.sql
- [x] Tarea 1.2: Implementar funciones de token en auth.php
- [x] Tarea 1.3: Modificar AuthController para "remember me"
- [x] Tarea 1.4: Actualizar vista de login
- [x] Tarea 2.1: Crear AdminController
- [x] Tarea 2.2: Añadir métodos a modelos User/Profile
- [x] Tarea 2.3: Crear vistas del panel admin
- [x] Tarea 2.4: Añadir rutas y protección

</details>
