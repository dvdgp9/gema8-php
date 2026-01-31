/**
 * Text-to-Speech Module using ElevenLabs API
 * High-quality voice synthesis for language learning
 */

(function() {
    'use strict';

    // Current audio element
    let currentAudio = null;
    let isLoading = false;
    let playbackRate = 1.0;

    class TTSManager {
        constructor() {
            this.isPlaying = false;
            this.currentText = '';
            this.currentLanguage = '';
        }

        /**
         * Speak text using ElevenLabs API
         * @param {string} text - Text to speak
         * @param {string} langCode - Gema8 language code
         * @param {Object} options - Optional callbacks
         * @returns {Promise}
         */
        async speak(text, langCode, options = {}) {
            if (!text || text.trim() === '') {
                if (options.onError) options.onError(new Error('No text to speak'));
                return Promise.reject(new Error('No text to speak'));
            }

            // Stop any current playback
            this.stop();

            if (options.onStart) options.onStart();
            isLoading = true;

            try {
                // Call our backend endpoint
                const response = await fetch(window.BASE_URL + '/api/tts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        text: text.trim(),
                        language: langCode
                    })
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.error || 'TTS request failed');
                }

                const data = await response.json();

                if (!data.audio) {
                    throw new Error('No audio data received');
                }

                // Create audio element from base64
                const audioSrc = 'data:audio/mp3;base64,' + data.audio;
                currentAudio = new Audio(audioSrc);
                currentAudio.playbackRate = playbackRate;

                // Store current state
                this.currentText = text;
                this.currentLanguage = langCode;

                return new Promise((resolve, reject) => {
                    currentAudio.onplay = () => {
                        this.isPlaying = true;
                        if (options.onPlay) options.onPlay();
                    };

                    currentAudio.onended = () => {
                        this.isPlaying = false;
                        isLoading = false;
                        if (options.onEnd) options.onEnd();
                        resolve();
                    };

                    currentAudio.onerror = (e) => {
                        this.isPlaying = false;
                        isLoading = false;
                        const error = new Error('Audio playback failed');
                        if (options.onError) options.onError(error);
                        reject(error);
                    };

                    currentAudio.play().catch(err => {
                        this.isPlaying = false;
                        isLoading = false;
                        if (options.onError) options.onError(err);
                        reject(err);
                    });
                });

            } catch (error) {
                isLoading = false;
                console.error('TTS Error:', error);
                if (options.onError) options.onError(error);
                return Promise.reject(error);
            }
        }

        /**
         * Stop current playback
         */
        stop() {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio = null;
            }
            this.isPlaying = false;
            isLoading = false;
        }

        /**
         * Pause current playback
         */
        pause() {
            if (currentAudio && this.isPlaying) {
                currentAudio.pause();
                this.isPlaying = false;
            }
        }

        /**
         * Resume paused playback
         */
        resume() {
            if (currentAudio && !this.isPlaying) {
                currentAudio.play();
                this.isPlaying = true;
            }
        }

        /**
         * Toggle play/pause
         */
        toggle() {
            if (this.isPlaying) {
                this.pause();
            } else {
                this.resume();
            }
        }

        /**
         * Set playback rate (speed)
         * @param {number} rate - 0.5 to 2.0
         */
        setRate(rate) {
            playbackRate = Math.max(0.5, Math.min(2.0, parseFloat(rate)));
            if (currentAudio) {
                currentAudio.playbackRate = playbackRate;
            }
        }

        /**
         * Get current playback rate
         * @returns {number}
         */
        getRate() {
            return playbackRate;
        }

        /**
         * Check if currently playing
         * @returns {boolean}
         */
        isSpeaking() {
            return this.isPlaying;
        }

        /**
         * Check if currently loading
         * @returns {boolean}
         */
        isLoading() {
            return isLoading;
        }

        /**
         * Get last spoken text for repeat
         * @returns {string}
         */
        getLastText() {
            return this.currentText;
        }

        /**
         * Get last language used
         * @returns {string}
         */
        getLastLanguage() {
            return this.currentLanguage;
        }

        /**
         * Repeat last spoken text
         * @param {Object} options - Optional callbacks
         * @returns {Promise}
         */
        async repeat(options = {}) {
            if (!this.currentText || !this.currentLanguage) {
                if (options.onError) options.onError(new Error('Nothing to repeat'));
                return Promise.reject(new Error('Nothing to repeat'));
            }
            return this.speak(this.currentText, this.currentLanguage, options);
        }
    }

    // Create global instance
    window.TTS = new TTSManager();

    // Legacy compatibility - global speakText function
    window.speakText = function(text, langCode, options) {
        return window.TTS.speak(text, langCode, options);
    };

    // Set BASE_URL if not defined
    if (!window.BASE_URL) {
        // Try to detect from current URL
        const path = window.location.pathname;
        const baseMatch = path.match(/^(.*?)(?:\/|$)/);
        window.BASE_URL = baseMatch ? baseMatch[1] || '' : '';
    }

})();
