<?php
/**
 * API Controller
 * Handles AJAX requests for translations, tips, whispers, etc.
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class ApiController extends Controller {
    /**
     * Translate text
     */
    public function translate(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $text = trim($input['text'] ?? '');
        $sourceLanguage = sanitize($input['source_language'] ?? 'english');
        $targetLanguage = sanitize($input['target_language'] ?? '');
        $ephemeral = (bool) ($input['ephemeral'] ?? false);
        
        // Validation
        if (empty($text)) {
            $this->json(['error' => 'Text is required'], 400);
        }
        
        if (empty($targetLanguage) || !isValidLanguage($targetLanguage) || !isValidLanguage($sourceLanguage)) {
            $this->json(['error' => 'Invalid source or target language'], 400);
        }
        
        // Check credits
        if (!hasCredits(CREDIT_COST_TRANSLATE)) {
            $this->json(['error' => 'Insufficient credits'], 402);
        }
        
        // Check for cached translation (unless ephemeral)
        if (!$ephemeral) {
            $normalizedText = normalizeText($text);
            $existing = Translation::findForUser(
                userId(), 
                $normalizedText, 
                $sourceLanguage, 
                $targetLanguage
            );
            
            if ($existing) {
                // Deduct credits
                if (!deductCredits(CREDIT_COST_TRANSLATE)) {
                    $this->json(['error' => 'Failed to process credits'], 500);
                }
                
                // Update count
                $translation = Translation::saveOrUpdate(
                    userId(),
                    $existing['original_text'],
                    $existing['translated_text'],
                    $sourceLanguage,
                    $targetLanguage
                );
                
                $this->json([
                    'original_text' => $translation['original_text'],
                    'translated_text' => $translation['translated_text'],
                    'source_language' => $sourceLanguage,
                    'target_language' => $targetLanguage,
                    'count' => $translation['count'],
                    'cached' => true
                ]);
            }
        }
        
        // Translate via Gemini
        $translatedText = Gemini::translate($text, $sourceLanguage, $targetLanguage);
        
        if (!$translatedText) {
            $this->json(['error' => 'Translation failed'], 500);
        }
        
        // Deduct credits
        if (!deductCredits(CREDIT_COST_TRANSLATE)) {
            $this->json(['error' => 'Failed to process credits'], 500);
        }
        
        // Save translation (unless ephemeral)
        if (!$ephemeral) {
            $translation = Translation::saveOrUpdate(
                userId(),
                $text,
                $translatedText,
                $sourceLanguage,
                $targetLanguage
            );
            
            $this->json([
                'original_text' => $text,
                'translated_text' => $translatedText,
                'source_language' => $sourceLanguage,
                'target_language' => $targetLanguage,
                'count' => $translation['count'] ?? 1,
                'ephemeral' => false
            ]);
        }
        
        $this->json([
            'original_text' => $text,
            'translated_text' => $translatedText,
            'source_language' => $sourceLanguage,
            'target_language' => $targetLanguage,
            'ephemeral' => true
        ]);
    }
    
    /**
     * Ask language question
     */
    public function askQuestion(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $question = trim($input['question'] ?? '');
        $language = sanitize($input['language'] ?? 'indonesian');
        
        if (empty($question)) {
            $this->json(['error' => 'Question is required'], 400);
        }
        
        if (!isValidLanguage($language)) {
            $this->json(['error' => 'Invalid language'], 400);
        }
        
        // Check credits
        if (!hasCredits(CREDIT_COST_ASK)) {
            $this->json(['error' => 'Insufficient credits'], 402);
        }
        
        // Ask Gemini
        $answer = Gemini::askLanguageQuestion($question, $language);
        
        if (!$answer) {
            $this->json(['error' => 'Failed to get answer'], 500);
        }
        
        // Deduct credits
        if (!deductCredits(CREDIT_COST_ASK)) {
            $this->json(['error' => 'Failed to process credits'], 500);
        }
        
        $this->json(['answer' => $answer]);
    }
    
    /**
     * Generate whisper (situational phrases)
     */
    public function generateWhisper(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $situation = trim($input['situation'] ?? '');
        $targetLanguage = sanitize($input['target_language'] ?? 'indonesian');
        
        if (empty($situation)) {
            $this->json(['error' => 'Situation description is required'], 400);
        }
        
        if (!isValidLanguage($targetLanguage)) {
            $this->json(['error' => 'Invalid target language'], 400);
        }
        
        // Check credits
        if (!hasCredits(CREDIT_COST_WHISPER)) {
            $this->json(['error' => 'Insufficient credits'], 402);
        }
        
        // Generate via Gemini
        $result = Gemini::generateWhisper($situation, $targetLanguage);
        
        if (!$result) {
            $this->json(['error' => 'Failed to generate phrases'], 500);
        }
        
        // Deduct credits
        if (!deductCredits(CREDIT_COST_WHISPER)) {
            $this->json(['error' => 'Failed to process credits'], 500);
        }
        
        // Save whisper
        $whisper = Whisper::create(
            userId(),
            $result['title'],
            $situation,
            $targetLanguage,
            $result['phrases']
        );
        
        if (!$whisper) {
            $this->json(['error' => 'Failed to save whisper'], 500);
        }
        
        $this->json($whisper);
    }
    
    /**
     * Generate daily tip
     */
    public function generateTip(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $language = sanitize($input['language'] ?? '');
        
        if (empty($language) || !isValidLanguage($language)) {
            $this->json(['error' => 'Invalid language'], 400);
        }
        
        // Check for existing tip today
        $existingTip = Tip::getTodaysTip(userId(), $language);
        
        if ($existingTip) {
            $this->json([
                'tip' => $existingTip,
                'language' => $language,
                'cached' => true
            ]);
        }
        
        // Update language progress
        Profile::updateLanguage(userId(), $language);
        refreshProfile();
        
        // Get progress for tip generation
        $progress = Profile::getLanguageProgress(userId(), $language);
        $daysActive = $progress['days_active'] ?? 1;
        
        // Get recent topics for anti-repetition
        $recentTopics = Tip::getRecentSummaries(userId(), $language);
        
        // Generate tip
        $tip = Gemini::generateDailyTip($language, $daysActive, $recentTopics);
        
        if (!$tip) {
            $this->json(['error' => 'Failed to generate tip'], 500);
        }
        
        // Store tip
        $briefSummary = strtok($tip, '.') . '.';
        Tip::store(userId(), $language, $tip, $briefSummary);
        
        $this->json([
            'tip' => $tip,
            'language' => $language,
            'days_active' => $daysActive,
            'cached' => false
        ]);
    }
    
    /**
     * Delete translation
     */
    public function deleteTranslation(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int) ($input['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['error' => 'Invalid translation ID'], 400);
        }
        
        if (Translation::delete($id, userId())) {
            $this->json(['success' => true]);
        }
        
        $this->json(['error' => 'Failed to delete translation'], 500);
    }
    
    /**
     * Delete whisper
     */
    public function deleteWhisper(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int) ($input['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['error' => 'Invalid whisper ID'], 400);
        }
        
        if (Whisper::delete($id, userId())) {
            $this->json(['success' => true]);
        }
        
        $this->json(['error' => 'Failed to delete whisper'], 500);
    }
    
    /**
     * Text-to-Speech using ElevenLabs
     * Returns audio/mpeg stream
     */
    public function tts(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $text = trim($input['text'] ?? '');
        $language = sanitize($input['language'] ?? 'english');
        
        if (empty($text)) {
            $this->json(['error' => 'Text is required'], 400);
        }
        
        if (mb_strlen($text) > 1000) {
            $this->json(['error' => 'Text too long (max 1000 characters)'], 400);
        }
        
        // Check credits
        if (!hasCredits(CREDIT_COST_TTS)) {
            $this->json(['error' => 'Insufficient credits'], 402);
        }
        
        // Generate audio
        $audio = ElevenLabs::textToSpeech($text, $language);
        
        if (!$audio) {
            $this->json(['error' => 'Failed to generate audio'], 500);
        }
        
        // Deduct credits
        if (!deductCredits(CREDIT_COST_TTS)) {
            $this->json(['error' => 'Failed to process credits'], 500);
        }
        
        // Return audio as base64 (for easier JS handling)
        $this->json([
            'audio' => base64_encode($audio),
            'format' => 'audio/mpeg'
        ]);
    }
}
