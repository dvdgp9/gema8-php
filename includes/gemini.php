<?php
/**
 * Google Gemini API Integration
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Gemini {
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    
    /**
     * Send request to Gemini API
     */
    private static function request(string $prompt): ?string {
        $apiKey = GEMINI_API_KEY;
        
        if (empty($apiKey) || $apiKey === 'YOUR_GEMINI_API_KEY') {
            error_log('Gemini API key not configured');
            return null;
        }
        
        $url = self::API_URL . '?key=' . $apiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log('Gemini API cURL error: ' . $error);
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log('Gemini API HTTP error: ' . $httpCode . ' - ' . $response);
            return null;
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            error_log('Gemini API unexpected response: ' . $response);
            return null;
        }
        
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }
    
    /**
     * Translate text between languages
     */
    public static function translate(string $text, string $sourceLanguage, string $targetLanguage): ?string {
        $prompt = "Translate the following text from {$sourceLanguage} to {$targetLanguage}. " .
                  "Only provide the translation, nothing else. " .
                  "If the text is already in the target language, just return it as is.\n\n" .
                  "Text to translate: {$text}";
        
        $result = self::request($prompt);
        
        return $result ? trim($result) : null;
    }
    
    /**
     * Ask a language question
     */
    public static function askLanguageQuestion(string $question, string $language): ?string {
        $prompt = "You are an expert language teacher specializing in {$language}. " .
                  "Answer the following question about the {$language} language in a clear, " .
                  "concise, and educational way. Include examples when helpful. " .
                  "Keep your response under 300 words.\n\n" .
                  "Question: {$question}";
        
        return self::request($prompt);
    }
    
    /**
     * Generate situational phrases (whisper)
     */
    public static function generateWhisper(string $situation, string $targetLanguage): ?array {
        $prompt = <<<PROMPT
Generate practical phrases for learning {$targetLanguage} in this situation: "{$situation}"

IMPORTANT: Respond with ONLY valid JSON, no other text. Use this exact structure:
{"title":"Short Title Here","phrases":[{"target_sentence":"phrase in {$targetLanguage}","translation":"English meaning","pronunciation":"phonetic guide"}]}

Generate 8-10 phrases. Keep them simple and practical.
PROMPT;

        $result = self::request($prompt);
        
        if (!$result) {
            error_log('Gemini whisper: request returned null');
            return null;
        }
        
        error_log('Gemini whisper raw response: ' . substr($result, 0, 500));
        
        // Clean JSON from markdown code blocks and extra whitespace
        $result = preg_replace('/```json\s*|\s*```/', '', $result);
        $result = preg_replace('/```\s*|\s*```/', '', $result);
        $result = trim($result);
        
        // Try direct parse first
        $parsed = json_decode($result, true);
        
        if (!$parsed || !isset($parsed['title']) || !isset($parsed['phrases'])) {
            // Fallback: extract JSON object from response
            if (preg_match('/\{[^{}]*"title"[^{}]*"phrases"[^{}]*\[[\s\S]*\][\s\S]*\}/', $result, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
            
            // Second fallback: find any JSON object
            if (!$parsed && preg_match('/\{[\s\S]+\}/', $result, $matches)) {
                $parsed = json_decode($matches[0], true);
            }

            if (!$parsed || !isset($parsed['title']) || !isset($parsed['phrases'])) {
                error_log('Gemini whisper parse error. Response: ' . $result);
                return null;
            }
        }
        
        return $parsed;
    }
    
    /**
     * Generate daily tip
     */
    public static function generateDailyTip(string $language, int $daysActive, array $recentTopics = []): ?string {
        $isBasicLevel = $daysActive <= 21;
        $focusArea = $isBasicLevel 
            ? 'basic fundamentals like grammar basics, common phrases, pronunciation, or essential vocabulary'
            : 'cultural nuances, idioms, regional variations, social etiquette, or advanced grammar';
        
        $prompt = "Generate a brief but interesting daily tip about {$language} or the {$language} language " .
                  "that would be helpful for someone learning the language. Focus on {$focusArea}. " .
                  "Make it concise (50-100 words), educational and easy to understand. " .
                  "The tip should be in English.";
        
        if (!empty($recentTopics)) {
            $topicsList = implode(', ', $recentTopics);
            $prompt .= "\n\nAvoid these recent topics covered in the last 30 days: {$topicsList}";
        }
        
        return self::request($prompt);
    }
}
