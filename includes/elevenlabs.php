<?php
/**
 * ElevenLabs TTS API Integration
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class ElevenLabs {
    private const API_URL = 'https://api.elevenlabs.io/v1/text-to-speech';
    
    // Voz multilingüe de alta calidad (Rachel)
    private const DEFAULT_VOICE_ID = '21m00Tcm4TlvDq8ikWAM';
    
    // Mapeo de idiomas Gema8 a códigos de idioma ElevenLabs
    private static array $languageMap = [
        'indonesian' => 'id',
        'vietnamese' => 'vi',
        'french' => 'fr',
        'spanish' => 'es',
        'portuguese' => 'pt',
        'italian' => 'it',
        'german' => 'de',
        'dutch' => 'nl',
        'swedish' => 'sv',
        'norwegian' => 'no',
        'danish' => 'da',
        'finnish' => 'fi',
        'russian' => 'ru',
        'polish' => 'pl',
        'czech' => 'cs',
        'hungarian' => 'hu',
        'romanian' => 'ro',
        'bulgarian' => 'bg',
        'japanese' => 'ja',
        'korean' => 'ko',
        'chinese' => 'zh',
        'thai' => 'th',
        'hindi' => 'hi',
        'arabic' => 'ar',
        'hebrew' => 'he',
        'turkish' => 'tr',
        'greek' => 'el',
        'ukrainian' => 'uk',
        'croatian' => 'hr',
        'serbian' => 'sr',
        'english' => 'en'
    ];
    
    /**
     * Generate speech audio from text
     * Returns raw MP3 audio data or null on failure
     */
    public static function textToSpeech(string $text, string $language = 'english'): ?string {
        $apiKey = ELEVENLABS_API_KEY;
        
        if (empty($apiKey) || $apiKey === 'YOUR_ELEVENLABS_API_KEY') {
            error_log('ElevenLabs API key not configured');
            return null;
        }
        
        // Limitar longitud del texto (ElevenLabs tiene límites)
        $text = mb_substr(trim($text), 0, 1000);
        
        if (empty($text)) {
            return null;
        }
        
        $url = self::API_URL . '/' . self::DEFAULT_VOICE_ID;
        
        $data = [
            'text' => $text,
            'model_id' => 'eleven_multilingual_v2',
            'voice_settings' => [
                'stability' => 0.5,
                'similarity_boost' => 0.75,
                'style' => 0.0,
                'use_speaker_boost' => true
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Accept: audio/mpeg',
                'Content-Type: application/json',
                'xi-api-key: ' . $apiKey
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log('ElevenLabs API cURL error: ' . $error);
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log('ElevenLabs API HTTP error: ' . $httpCode . ' - ' . substr($response, 0, 500));
            return null;
        }
        
        // Verificar que recibimos audio
        if (strpos($contentType, 'audio') === false) {
            error_log('ElevenLabs API unexpected content type: ' . $contentType);
            return null;
        }
        
        return $response;
    }
    
    /**
     * Get language code for ElevenLabs from Gema8 language name
     */
    public static function getLanguageCode(string $language): string {
        return self::$languageMap[strtolower($language)] ?? 'en';
    }
}
