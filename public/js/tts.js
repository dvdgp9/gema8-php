/**
 * Text-to-Speech Module using Web Speech API
 * Supports multiple languages with voice selection
 */

(function() {
    'use strict';

    // Language code mapping from Gema8 to BCP-47
    const LANG_MAP = {
        'french': 'fr-FR',
        'spanish': 'es-ES',
        'german': 'de-DE',
        'italian': 'it-IT',
        'portuguese': 'pt-PT',
        'dutch': 'nl-NL',
        'russian': 'ru-RU',
        'japanese': 'ja-JP',
        'chinese': 'zh-CN',
        'korean': 'ko-KR',
        'arabic': 'ar-SA',
        'hindi': 'hi-IN',
        'turkish': 'tr-TR',
        'polish': 'pl-PL',
        'swedish': 'sv-SE',
        'norwegian': 'nb-NO',
        'danish': 'da-DK',
        'finnish': 'fi-FI',
        'greek': 'el-GR',
        'czech': 'cs-CZ',
        'hungarian': 'hu-HU',
        'romanian': 'ro-RO',
        'ukrainian': 'uk-UA',
        'hebrew': 'he-IL',
        'thai': 'th-TH',
        'vietnamese': 'vi-VN',
        'indonesian': 'id-ID',
        'malay': 'ms-MY',
        'english': 'en-US'
    };

    // Fallback mappings for broader language support
    const LANG_FALLBACK = {
        'fr': 'fr-FR',
        'es': 'es-ES',
        'de': 'de-DE',
        'it': 'it-IT',
        'pt': 'pt-PT'
    };

    class TTSManager {
        constructor() {
            this.synth = window.speechSynthesis;
            this.voices = [];
            this.currentUtterance = null;
            this.isPlaying = false;
            this.rate = 1.0;
            this.pitch = 1.0;
            
            this.init();
        }

        init() {
            if (!this.isSupported()) {
                console.warn('Web Speech API not supported in this browser');
                return;
            }

            // Load voices (they load asynchronously)
            this.loadVoices();
            
            // Voices may load after initial page load
            if (this.synth.onvoiceschanged !== undefined) {
                this.synth.onvoiceschanged = () => this.loadVoices();
            }
        }

        isSupported() {
            return 'speechSynthesis' in window;
        }

        loadVoices() {
            this.voices = this.synth.getVoices();
            console.log(`TTS: Loaded ${this.voices.length} voices`);
        }

        /**
         * Get the best voice for a given language code
         * @param {string} langCode - Gema8 language code (e.g., 'french', 'spanish')
         * @returns {SpeechSynthesisVoice|null}
         */
        getBestVoice(langCode) {
            if (!this.voices.length) {
                this.loadVoices();
            }

            const bcp47Code = LANG_MAP[langCode.toLowerCase()] || langCode;
            const langPrefix = bcp47Code.split('-')[0];

            // Priority 1: Exact match (e.g., fr-FR)
            let voice = this.voices.find(v => v.lang === bcp47Code);
            if (voice) return voice;

            // Priority 2: Language prefix match (e.g., fr-*)
            voice = this.voices.find(v => v.lang.startsWith(langPrefix + '-'));
            if (voice) return voice;

            // Priority 3: Fallback code
            const fallbackCode = LANG_FALLBACK[langPrefix];
            if (fallbackCode) {
                voice = this.voices.find(v => v.lang.startsWith(fallbackCode.split('-')[0]));
                if (voice) return voice;
            }

            // Priority 4: Any voice (better than nothing)
            return this.voices[0] || null;
        }

        /**
         * Speak text in the specified language
         * @param {string} text - Text to speak
         * @param {string} langCode - Gema8 language code
         * @param {Object} options - Optional settings
         * @returns {Promise}
         */
        speak(text, langCode, options = {}) {
            return new Promise((resolve, reject) => {
                if (!this.isSupported()) {
                    reject(new Error('Speech synthesis not supported'));
                    return;
                }

                if (!text || text.trim() === '') {
                    reject(new Error('No text to speak'));
                    return;
                }

                // Cancel any ongoing speech
                this.stop();

                const utterance = new SpeechSynthesisUtterance(text.trim());
                const voice = this.getBestVoice(langCode);

                if (voice) {
                    utterance.voice = voice;
                    utterance.lang = voice.lang;
                } else {
                    // Fallback to mapped language code
                    utterance.lang = LANG_MAP[langCode.toLowerCase()] || 'en-US';
                }

                utterance.rate = options.rate || this.rate;
                utterance.pitch = options.pitch || this.pitch;
                utterance.volume = options.volume || 1.0;

                utterance.onstart = () => {
                    this.isPlaying = true;
                    this.currentUtterance = utterance;
                    if (options.onStart) options.onStart();
                };

                utterance.onend = () => {
                    this.isPlaying = false;
                    this.currentUtterance = null;
                    if (options.onEnd) options.onEnd();
                    resolve();
                };

                utterance.onerror = (event) => {
                    this.isPlaying = false;
                    this.currentUtterance = null;
                    console.error('TTS Error:', event.error);
                    if (options.onError) options.onError(event);
                    reject(new Error(`Speech synthesis error: ${event.error}`));
                };

                this.synth.speak(utterance);
            });
        }

        /**
         * Stop current speech
         */
        stop() {
            if (this.synth.speaking) {
                this.synth.cancel();
            }
            this.isPlaying = false;
            this.currentUtterance = null;
        }

        /**
         * Pause current speech
         */
        pause() {
            if (this.synth.speaking && !this.synth.paused) {
                this.synth.pause();
            }
        }

        /**
         * Resume paused speech
         */
        resume() {
            if (this.synth.paused) {
                this.synth.resume();
            }
        }

        /**
         * Toggle play/pause
         */
        toggle() {
            if (this.synth.paused) {
                this.resume();
            } else if (this.synth.speaking) {
                this.pause();
            }
        }

        /**
         * Set speech rate (0.1 to 10)
         * @param {number} rate
         */
        setRate(rate) {
            this.rate = Math.max(0.1, Math.min(10, rate));
        }

        /**
         * Get current rate
         * @returns {number}
         */
        getRate() {
            return this.rate;
        }

        /**
         * Check if currently speaking
         * @returns {boolean}
         */
        isSpeaking() {
            return this.isPlaying;
        }

        /**
         * Get available voices for a language
         * @param {string} langCode
         * @returns {SpeechSynthesisVoice[]}
         */
        getVoicesForLanguage(langCode) {
            const bcp47Code = LANG_MAP[langCode.toLowerCase()] || langCode;
            const langPrefix = bcp47Code.split('-')[0];
            
            return this.voices.filter(v => 
                v.lang === bcp47Code || 
                v.lang.startsWith(langPrefix + '-')
            );
        }

        /**
         * Show browser warning if not supported
         * @returns {string|null}
         */
        getWarningMessage() {
            if (!this.isSupported()) {
                return 'Text-to-speech is not supported in your browser. Please use Chrome, Edge, Safari, or Firefox.';
            }
            return null;
        }
    }

    // Create global instance
    window.TTS = new TTSManager();

    // Utility function for quick speech
    window.speakText = function(text, langCode, options) {
        return window.TTS.speak(text, langCode, options);
    };

})();
