/**
 * Gema8 Text-to-Speech Module
 * Handles audio generation and playback using ElevenLabs API
 */

class TTSManager {
    constructor() {
        this.currentAudio = null;
        this.isPlaying = false;
        this.audioCache = new Map();
    }

    /**
     * Generate and play audio for text
     * @param {string} text - Text to speak
     * @param {string} language - Language code
     * @param {HTMLElement} buttonElement - Button that triggered the action
     */
    async speak(text, language, buttonElement = null) {
        if (!text || text.trim() === '') {
            console.warn('TTS: Empty text provided');
            return;
        }

        // Stop if already playing this text
        if (this.isPlaying && this.currentText === text) {
            this.stop();
            this.updateButtonState(buttonElement, 'idle');
            return;
        }

        this.currentText = text;
        this.updateButtonState(buttonElement, 'loading');

        try {
            // Check cache first
            const cacheKey = `${text}_${language}`;
            if (this.audioCache.has(cacheKey)) {
                await this.playAudio(this.audioCache.get(cacheKey), buttonElement);
                return;
            }

            // Fetch audio from API
            const base64Audio = await this.fetchAudio(text, language);
            
            if (base64Audio) {
                this.audioCache.set(cacheKey, base64Audio);
                await this.playAudio(base64Audio, buttonElement);
            }
        } catch (error) {
            console.error('TTS Error:', error);
            this.updateButtonState(buttonElement, 'error');
            if (typeof showToast === 'function') {
                showToast('Failed to generate audio', 'error');
            }
        }
    }

    /**
     * Fetch audio from ElevenLabs API via backend
     */
    async fetchAudio(text, language) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        const response = await fetch(window.BASE_URL + '/api/text-to-speech', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken || ''
            },
            body: JSON.stringify({
                text: text,
                language: language
            })
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to generate audio');
        }

        const data = await response.json();
        return data.audio;
    }

    /**
     * Play base64 audio
     */
    async playAudio(base64Audio, buttonElement) {
        const audioSrc = 'data:audio/mp3;base64,' + base64Audio;
        
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio = null;
        }

        this.currentAudio = new Audio(audioSrc);
        this.isPlaying = true;
        this.updateButtonState(buttonElement, 'playing');

        this.currentAudio.addEventListener('ended', () => {
            this.isPlaying = false;
            this.updateButtonState(buttonElement, 'idle');
        });

        this.currentAudio.addEventListener('error', () => {
            this.isPlaying = false;
            this.updateButtonState(buttonElement, 'error');
        });

        await this.currentAudio.play();
    }

    /**
     * Stop current playback
     */
    stop() {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio = null;
        }
        this.isPlaying = false;
    }

    /**
     * Update button visual state
     */
    updateButtonState(button, state) {
        if (!button) return;

        const icon = button.querySelector('i');
        
        // Reset classes
        button.classList.remove('playing', 'loading', 'error');
        if (icon) {
            icon.setAttribute('data-lucide', 'volume-2');
        }

        switch (state) {
            case 'loading':
                button.classList.add('loading');
                button.innerHTML = '<span class="spinner h-4 w-4"></span>';
                button.disabled = true;
                break;
            
            case 'playing':
                button.classList.add('playing');
                button.innerHTML = '<i data-lucide="square" class="h-4 w-4"></i>';
                button.disabled = false;
                break;
            
            case 'error':
                button.classList.add('error');
                button.innerHTML = '<i data-lucide="volume-x" class="h-4 w-4"></i>';
                button.disabled = false;
                setTimeout(() => this.updateButtonState(button, 'idle'), 2000);
                break;
            
            case 'idle':
            default:
                button.innerHTML = '<i data-lucide="volume-2" class="h-4 w-4"></i>';
                button.disabled = false;
                break;
        }

        // Refresh Lucide icons if available
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

// Global instance
const ttsManager = new TTSManager();

/**
 * Helper function to create audio button
 * @param {string} text - Text to speak
 * @param {string} language - Language
 * @returns {HTMLButtonElement}
 */
function createAudioButton(text, language) {
    const button = document.createElement('button');
    button.className = 'btn-audio inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-600 hover:bg-primary-200 transition-colors';
    button.innerHTML = '<i data-lucide="volume-2" class="h-4 w-4"></i>';
    button.title = 'Listen';
    button.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        ttsManager.speak(text, language, button);
    };
    return button;
}

/**
 * Add audio button to an element
 * @param {HTMLElement} container - Container element
 * @param {string} text - Text to speak
 * @param {string} language - Language
 */
function addAudioButton(container, text, language) {
    if (!container) return;
    
    // Remove existing audio button
    const existing = container.querySelector('.btn-audio');
    if (existing) {
        existing.remove();
    }

    const button = createAudioButton(text, language);
    container.appendChild(button);

    // Initialize icon
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { TTSManager, ttsManager, createAudioButton, addAudioButton };
}
