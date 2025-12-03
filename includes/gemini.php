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
You are helping someone learn {$targetLanguage}. They are about to encounter this situation: "{$situation}"

Generate:
1. A short, descriptive title for this situation (3-5 words)
2. 10-12 practical phrases they would need in this situation

For each phrase, provide:
- The {$targetLanguage} sentence (keep it simple and practical)
- English translation
- Pronunciation guide (phonetic, easy to read)

Format your response as JSON:
{
  "title": "Short descriptive title",
  "phrases": [
    {
      "target_sentence": "{$targetLanguage} phrase",
      "translation": "English translation",
      "pronunciation": "phonetic guide"
    }
  ]
}

Make the phrases practical, simple, and useful for real communication. Focus on common expressions someone would actually need.
PROMPT;

        $result = self::request($prompt);
        
        if (!$result) {
            return null;
        }
        
        // Clean JSON from markdown code blocks
        $result = preg_replace('/```json\s*|\s*```/', '', $result);
        $result = trim($result);
        
        $parsed = json_decode($result, true);
        
        if (!$parsed || !isset($parsed['title']) || !isset($parsed['phrases'])) {
            error_log('Gemini whisper parse error: ' . $result);
            return null;
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
