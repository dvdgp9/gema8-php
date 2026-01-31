# Gema8 - Integración de Audio con ElevenLabs

## Background and Motivation
El usuario necesita poder escuchar las traducciones generadas. Anteriormente se intentó con Web Speech API pero se prefiere ElevenLabs por su superior calidad de voz, especialmente crítica para el aprendizaje de idiomas donde la pronunciación correcta es fundamental.

## Key Challenges and Analysis
- **Gestión de API Key:** Debe estar en el lado del servidor para seguridad.
- **Latencia:** Generar audio en tiempo real puede tardar 1-2 segundos.
- **Coste/Tokens:** ElevenLabs tiene límites, se debería considerar caché si el mismo texto se repite mucho (opcional para MVP).
- **Mapeo de Voces:** Necesitamos seleccionar voces adecuadas de ElevenLabs para cada idioma soportado.

## High-level Task Breakdown

### Tarea 1: Configuración Backend
- [x] Añadir `ELEVENLABS_API_KEY` a `config/config.php`
- [ ] Crear endpoint `/api/tts` en `ApiController.php` que conecte con ElevenLabs
- [ ] Implementar lógica de selección de voz por idioma

### Tarea 2: Frontend e Interfaz
- [ ] Crear `public/js/audio-player.js` para manejar la carga y reproducción del audio
- [ ] Añadir botones de "Escuchar" con iconos de Lucide en `views/dashboard/index.php`
- [ ] Añadir botones de audio en `views/whispers/index.php`

### Tarea 3: Pulido y UX
- [ ] Añadir estados de carga (spinner) mientras se genera el audio
- [ ] Asegurar que el reproductor se limpie correctamente para evitar fugas de memoria

## Project Status Board
- [ ] Tarea 1: Backend ElevenLabs
- [ ] Tarea 2: Frontend JS & UI
- [ ] Tarea 3: Pruebas y Ajustes

## Executor's Feedback or Assistance Requests
**PROCEDIENDO CON LA IMPLEMENTACIÓN** - Iniciando con la configuración de la API Key.

## Lessons
- ElevenLabs ofrece voces mucho más naturales que la Web Speech API integrada en navegadores.
- Es vital no exponer la API key en el frontend.
