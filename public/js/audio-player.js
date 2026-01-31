/**
 * Gema8 - Audio Player
 * Handles Text-to-Speech via ElevenLabs API
 */

const AudioPlayer = {
    currentAudio: null,
    isPlaying: false,

    /**
     * Play TTS for a given text and language
     * @param {string} text - The text to speak
     * @param {string} language - The language of the text
     * @param {HTMLElement} button - The button element that triggered the play (for UI feedback)
     */
    async play(text, language, button = null) {
        if (this.isPlaying) {
            this.stop();
        }

        if (!text) return;

        try {
            if (button) {
                button.classList.add('loading-audio');
                button.disabled = true;
            }

            const response = await fetch('/api/tts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ text, language })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Failed to generate audio');
            }

            const blob = await response.blob();
            const url = URL.createObjectURL(blob);

            this.currentAudio = new Audio(url);
            this.isPlaying = true;

            if (button) {
                button.classList.remove('loading-audio');
                button.classList.add('playing-audio');
            }

            this.currentAudio.onended = () => {
                this.stop(button);
                URL.revokeObjectURL(url);
            };

            this.currentAudio.onerror = () => {
                this.stop(button);
                URL.revokeObjectURL(url);
                console.error('Audio playback error');
            };

            await this.currentAudio.play();

        } catch (error) {
            console.error('TTS Error:', error);
            if (button) {
                button.classList.remove('loading-audio');
                button.disabled = false;
                
                // Temporary error visual feedback
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="lucide-alert-circle text-red-500"></i>';
                setTimeout(() => {
                    button.innerHTML = originalContent;
                }, 2000);
            }
        }
    },

    /**
     * Stop current playback
     * @param {HTMLElement} button - The button to reset UI for
     */
    stop(button = null) {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio = null;
        }
        this.isPlaying = false;
        if (button) {
            button.classList.remove('playing-audio');
            button.disabled = false;
        }
        
        // Find all audio buttons and reset them just in case
        document.querySelectorAll('.btn-audio').forEach(btn => {
            btn.classList.remove('playing-audio', 'loading-audio');
            btn.disabled = false;
        });
    }
};

// Global helper for onclick events
window.playAudio = (text, language, btn) => AudioPlayer.play(text, language, btn);
