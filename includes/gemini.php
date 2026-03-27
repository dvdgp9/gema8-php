<?php
/**
 * Google Gemini API Integration
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Gemini {
    private const OPENROUTER_URL = 'https://openrouter.ai/api/v1/chat/completions';
    private const MODEL = 'google/gemini-2.5-flash';
    
    /**
     * Send request via OpenRouter API (OpenAI-compatible)
     */
    private static function request(string $prompt): ?string {
        $apiKey = defined('OPENROUTER_API_KEY') ? OPENROUTER_API_KEY : '';
        
        if (empty($apiKey) || $apiKey === 'YOUR_OPENROUTER_API_KEY') {
            error_log('OpenRouter API key not configured');
            return null;
        }
        
        $data = [
            'model' => self::MODEL,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'top_p' => 0.95,
            'max_tokens' => 2048,
        ];
        
        $ch = curl_init(self::OPENROUTER_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
                'HTTP-Referer: ' . BASE_URL,
                'X-Title: Gema8'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log('OpenRouter cURL error: ' . $error);
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log('OpenRouter HTTP error: ' . $httpCode . ' - ' . $response);
            return null;
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['choices'][0]['message']['content'])) {
            error_log('OpenRouter unexpected response: ' . $response);
            return null;
        }
        
        return $result['choices'][0]['message']['content'];
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
    public static function askLanguageQuestion(
        string $question,
        string $language,
        string $responseLanguage = 'english'
    ): ?string {
        $prompt = "You are an expert language teacher specializing in {$language}. " .
                  "Answer the following question about the {$language} language in a clear, " .
                  "concise, and educational way. Include examples when helpful. " .
                  "Keep your response under 300 words. " .
                  "Write the full answer in {$responseLanguage}, even if the question is asked in another language.\n\n" .
                  "Question: {$question}";
        
        return self::request($prompt);
    }
    
    /**
     * Generate situational phrases (whisper)
     */
    public static function generateWhisper(
        string $situation,
        string $targetLanguage,
        string $translationLanguage = 'english'
    ): ?array {
        $prompt = <<<PROMPT
Generate practical phrases for learning {$targetLanguage} in this situation: "{$situation}"

IMPORTANT: Respond with ONLY valid JSON, no other text. Use this exact structure:
{"title":"Short Title Here","phrases":[{"target_sentence":"phrase in {$targetLanguage}","translation":"meaning in {$translationLanguage}","pronunciation":"phonetic guide"}]}

Generate 8-10 phrases. Keep them simple and practical.
Write the title and each "translation" value in {$translationLanguage}.
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
     * Translate in conversation context
     * Sends recent messages for context-aware translation
     */
    public static function conversationTranslate(
        string $text,
        string $direction,
        string $targetLanguage,
        string $level,
        string $tone,
        string $fidelity,
        array $recentMessages = [],
        ?string $summary = null
    ): ?array {
        $userLanguage = 'english';
        
        if ($direction === 'me') {
            $fromLang = $userLanguage;
            $toLang = $targetLanguage;
        } else {
            $fromLang = $targetLanguage;
            $toLang = $userLanguage;
        }
        
        $levelMap = [
            'beginner' => 'Use simple, basic vocabulary and short sentences. Avoid complex grammar.',
            'intermediate' => 'Use everyday vocabulary with moderate complexity. Natural but not too advanced.',
            'advanced' => 'Use rich vocabulary, idiomatic expressions, and natural native-like phrasing.'
        ];
        
        $toneMap = [
            'keep' => 'Maintain the original tone and register of the message.',
            'formal' => 'Use formal, polite, and respectful language.',
            'casual' => 'Use casual, friendly, relaxed language.',
            'funny' => 'Use a humorous, playful, lighthearted tone while preserving meaning.'
        ];
        
        $fidelityMap = [
            'literal' => 'Translate as literally as possible, keeping word order and structure close to the original.',
            'natural' => 'Translate naturally. Reorganize phrasing if needed so it sounds fluent in the target language.',
            'free' => 'Translate freely. Focus on conveying the intent and emotion. You may change structure significantly.'
        ];
        
        $levelInstruction = $levelMap[$level] ?? $levelMap['intermediate'];
        $toneInstruction = $toneMap[$tone] ?? $toneMap['keep'];
        $fidelityInstruction = $fidelityMap[$fidelity] ?? $fidelityMap['natural'];
        
        // Build conversation context
        $contextBlock = '';
        if ($summary) {
            $contextBlock .= "Summary of earlier conversation:\n{$summary}\n\n";
        }
        if (!empty($recentMessages)) {
            $contextBlock .= "Recent messages in this conversation:\n";
            foreach ($recentMessages as $msg) {
                $who = $msg['direction'] === 'me' ? 'User' : 'Other person';
                $contextBlock .= "- {$who}: {$msg['original_text']} → {$msg['translated_text']}\n";
            }
            $contextBlock .= "\n";
        }
        
        $prompt = <<<PROMPT
You are a real-time conversation translator helping a traveler communicate with a local person in {$targetLanguage}.

{$contextBlock}Now translate the following message from {$fromLang} to {$toLang}.

Translation settings:
- Level: {$levelInstruction}
- Tone: {$toneInstruction}
- Fidelity: {$fidelityInstruction}

IMPORTANT: Respond with ONLY valid JSON, no other text. Use this exact structure:
{"translated_text":"the translation here","cultural_note":null}

If there is an important cultural note about this message (e.g. something that could be misunderstood, a politeness issue, or a cultural context the traveler should know), include it in "cultural_note" as a brief string. Otherwise keep it null.

Message to translate: {$text}
PROMPT;

        $result = self::request($prompt);
        
        if (!$result) {
            return null;
        }
        
        // Clean JSON from markdown code blocks
        $result = preg_replace('/```json\s*|\s*```/', '', $result);
        $result = preg_replace('/```\s*|\s*```/', '', $result);
        $result = trim($result);
        
        $parsed = json_decode($result, true);
        
        if (!$parsed || !isset($parsed['translated_text'])) {
            // Fallback: extract JSON
            if (preg_match('/\{[\s\S]*"translated_text"[\s\S]*\}/', $result, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
            if (!$parsed || !isset($parsed['translated_text'])) {
                error_log('Gemini conversation translate parse error. Response: ' . $result);
                // Last resort: use raw response as translation
                return [
                    'translated_text' => trim($result),
                    'cultural_note' => null
                ];
            }
        }
        
        return [
            'translated_text' => $parsed['translated_text'],
            'cultural_note' => $parsed['cultural_note'] ?? null
        ];
    }
    
    /**
     * Generate conversation summary for context windowing
     */
    public static function summarizeConversation(array $messages, string $targetLanguage): ?string {
        $messageList = '';
        foreach ($messages as $msg) {
            $who = $msg['direction'] === 'me' ? 'User' : 'Other person';
            $messageList .= "- {$who}: {$msg['original_text']}\n";
        }
        
        $prompt = <<<PROMPT
Summarize the following conversation between a traveler and a local {$targetLanguage} speaker.
Keep it concise (3-5 sentences). Include key topics discussed, any agreements made, and important context.

Conversation:
{$messageList}
PROMPT;

        return self::request($prompt);
    }
    
    /**
     * Generate daily tip
     */
    public static function generateDailyTip(
        string $language,
        int $daysActive,
        array $recentTopics = [],
        string $outputLanguage = 'english'
    ): ?string {
        $isBasicLevel = $daysActive <= 21;
        $focusArea = $isBasicLevel 
            ? 'basic fundamentals like grammar basics, common phrases, pronunciation, or essential vocabulary'
            : 'cultural nuances, idioms, regional variations, social etiquette, or advanced grammar';
        
        $prompt = "Generate a brief but interesting daily tip about {$language} or the {$language} language " .
                  "that would be helpful for someone learning the language. Focus on {$focusArea}. " .
                  "Make it concise (50-100 words), educational and easy to understand. " .
                  "Write the tip in {$outputLanguage}.";
        
        if (!empty($recentTopics)) {
            $topicsList = implode(', ', $recentTopics);
            $prompt .= "\n\nAvoid these recent topics covered in the last 30 days: {$topicsList}";
        }
        
        return self::request($prompt);
    }
}
