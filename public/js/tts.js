/**
 * TTS Module - ElevenLabs Text-to-Speech
 */

const TTS = {
    currentAudio: null,
    isPlaying: false,
    
    /**
     * Play text using ElevenLabs TTS
     */
    async play(text, language, button) {
        // Si ya está reproduciendo el mismo audio, pausar
        if (this.isPlaying && this.currentAudio) {
            this.stop();
            return;
        }
        
        // Detener cualquier audio anterior
        this.stop();
        
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner"></span>';
        
        try {
            const response = await api(window.BASE_URL + '/api/tts', {
                text: text,
                language: language
            });
            
            // Decodificar base64 a blob
            const audioData = atob(response.audio);
            const audioArray = new Uint8Array(audioData.length);
            for (let i = 0; i < audioData.length; i++) {
                audioArray[i] = audioData.charCodeAt(i);
            }
            const blob = new Blob([audioArray], { type: 'audio/mpeg' });
            const audioUrl = URL.createObjectURL(blob);
            
            // Crear y reproducir audio
            this.currentAudio = new Audio(audioUrl);
            this.isPlaying = true;
            
            // Actualizar botón a estado "playing"
            button.innerHTML = '<i data-lucide="square" class="h-4 w-4"></i>';
            button.classList.add('!bg-primary-100', '!text-primary-700');
            if (window.lucide) lucide.createIcons();
            
            this.currentAudio.onended = () => {
                this.isPlaying = false;
                button.innerHTML = originalContent;
                button.classList.remove('!bg-primary-100', '!text-primary-700');
                if (window.lucide) lucide.createIcons();
                URL.revokeObjectURL(audioUrl);
            };
            
            this.currentAudio.onerror = () => {
                this.isPlaying = false;
                button.innerHTML = originalContent;
                button.classList.remove('!bg-primary-100', '!text-primary-700');
                if (window.lucide) lucide.createIcons();
                showToast('Error playing audio', 'error');
            };
            
            await this.currentAudio.play();
            
            // Actualizar créditos
            if (typeof updateCredits === 'function') {
                updateCredits();
            }
            
        } catch (error) {
            button.innerHTML = originalContent;
            if (window.lucide) lucide.createIcons();
            showToast(error.message || 'Failed to generate audio', 'error');
        } finally {
            button.disabled = false;
        }
    },
    
    /**
     * Stop current audio
     */
    stop() {
        if (this.currentAudio) {
            this.currentAudio.pause();
            this.currentAudio.currentTime = 0;
            this.currentAudio = null;
        }
        this.isPlaying = false;
    }
};
