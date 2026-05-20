<?php
/**
 * ResumeController — Handles AI resume enhancement via Gemini API
 */
class ResumeController {
    
    /**
     * POST /api/resume/enhance
     * Enhances resume content using Gemini AI
     */
    public static function apiEnhance(): void {
        global $pdo;
        session_start();
        
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input']);
            return;
        }

        $action = $input['action'] ?? 'enhance_summary';
        $data   = $input['data'] ?? [];

        $apiKey = self::getApiKey();
        if (!$apiKey) {
            // Fallback: return template-based suggestions without AI
            echo json_encode(self::fallbackEnhance($action, $data));
            return;
        }

        try {
            $result = self::callGemini($apiKey, $action, $data);
            echo json_encode(['success' => true, 'result' => $result]);
        } catch (Exception $e) {
            // Fallback on API error
            echo json_encode(self::fallbackEnhance($action, $data));
        }
    }

    /**
     * POST /api/resume/suggestions
     * Gets AI suggestions for the entire resume
     */
    public static function apiSuggestions(): void {
        global $pdo;
        session_start();
        
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $resumeData = $input['resume'] ?? [];

        $apiKey = self::getApiKey();
        if (!$apiKey) {
            echo json_encode(self::fallbackSuggestions($resumeData));
            return;
        }

        try {
            $prompt = self::buildSuggestionsPrompt($resumeData);
            $raw = self::rawGeminiCall($apiKey, $prompt);
            
            // Parse structured suggestions
            $suggestions = self::parseSuggestions($raw);
            echo json_encode(['success' => true, 'suggestions' => $suggestions]);
        } catch (Exception $e) {
            echo json_encode(self::fallbackSuggestions($resumeData));
        }
    }

    // ─── Gemini API Call ───────────────────────────────────

    private static function callGemini(string $apiKey, string $action, array $data): string {
        $prompt = match($action) {
            'enhance_summary' => self::summaryPrompt($data),
            'enhance_bullets' => self::bulletsPrompt($data),
            'suggest_skills'  => self::skillsPrompt($data),
            'tailor_for_job'  => self::tailorPrompt($data),
            default           => self::summaryPrompt($data),
        };

        return self::rawGeminiCall($apiKey, $prompt);
    }

    private static function rawGeminiCall(string $apiKey, string $prompt): string {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

        $payload = json_encode([
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024,
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Gemini API error: HTTP {$httpCode}");
        }

        $decoded = json_decode($response, true);
        return $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    // ─── Prompt Builders ──────────────────────────────────

    private static function summaryPrompt(array $data): string {
        $name = $data['name'] ?? '';
        $title = $data['title'] ?? '';
        $current = $data['current_summary'] ?? '';
        $skills = $data['skills'] ?? '';
        $experience = $data['experience'] ?? '';
        
        return "You are an expert resume writer. Rewrite this professional summary to be compelling, concise (3-4 sentences max), and ATS-optimized. Use strong action verbs and quantifiable achievements where possible.

Name: {$name}
Target Role: {$title}
Skills: {$skills}
Experience Context: {$experience}
Current Summary: {$current}

Return ONLY the improved summary text, no quotes, no labels, no explanation.";
    }

    private static function bulletsPrompt(array $data): string {
        $title = $data['job_title'] ?? '';
        $company = $data['company'] ?? '';
        $description = $data['description'] ?? '';

        return "You are an expert resume writer. Improve these work experience bullet points to be more impactful. Use the STAR method (Situation, Task, Action, Result). Include metrics and quantifiable outcomes where possible. Each bullet should start with a strong action verb.

Job Title: {$title}
Company: {$company}
Current Description: {$description}

Return exactly 3-4 improved bullet points, each on a new line starting with '• '. No other text.";
    }

    private static function skillsPrompt(array $data): string {
        $title = $data['target_role'] ?? '';
        $currentSkills = $data['current_skills'] ?? '';
        $experience = $data['experience'] ?? '';

        return "Based on this person's target role and experience, suggest 5-8 additional relevant skills they should add to their resume. Only suggest skills that are commonly sought by ATS systems for this role.

Target Role: {$title}
Current Skills: {$currentSkills}
Experience: {$experience}

Return ONLY a comma-separated list of skills. No explanations.";
    }

    private static function tailorPrompt(array $data): string {
        $resume = $data['resume_text'] ?? '';
        $jobDescription = $data['job_description'] ?? '';

        return "You are an expert resume consultant. Analyze this resume against the target job description and provide specific, actionable improvements.

RESUME:
{$resume}

JOB DESCRIPTION:
{$jobDescription}

Provide exactly 4 specific suggestions to tailor this resume for the job. Format each as a single concise sentence. Return each suggestion on a new line starting with '→ '.";
    }

    private static function buildSuggestionsPrompt(array $resume): string {
        $name = $resume['name'] ?? '';
        $title = $resume['title'] ?? '';
        $summary = $resume['summary'] ?? '';
        $skills = $resume['skills'] ?? '';
        $expCount = count($resume['experience'] ?? []);
        $eduCount = count($resume['education'] ?? []);

        return "You are an ATS resume expert. Review this resume and provide 3-4 actionable improvement tips. Score the resume 1-100 for ATS compatibility.

Name: {$name} | Target: {$title}
Summary: {$summary}
Skills: {$skills}
Experience entries: {$expCount}
Education entries: {$eduCount}

Format your response as JSON: {\"score\": 82, \"tips\": [\"tip1\", \"tip2\", \"tip3\"]}
Return ONLY valid JSON, nothing else.";
    }

    // ─── Fallback (no API key) ────────────────────────────

    private static function fallbackEnhance(string $action, array $data): array {
        return match($action) {
            'enhance_summary' => [
                'success' => true,
                'result' => self::templateSummary($data),
            ],
            'enhance_bullets' => [
                'success' => true,
                'result' => self::templateBullets($data),
            ],
            'suggest_skills' => [
                'success' => true,
                'result' => self::templateSkills($data),
            ],
            default => [
                'success' => true,
                'result' => 'AI enhancement requires a Gemini API key. Add GEMINI_API_KEY to config/ai.php.',
            ],
        };
    }

    private static function templateSummary(array $data): string {
        $title = $data['title'] ?? 'professional';
        $skills = $data['skills'] ?? '';
        $topSkills = $skills ? implode(', ', array_slice(explode(',', $skills), 0, 3)) : 'diverse technical skills';
        return "Results-driven {$title} with proven expertise in {$topSkills}. Passionate about delivering high-impact solutions and driving measurable business outcomes. Strong communicator with a track record of cross-functional collaboration and continuous improvement.";
    }

    private static function templateBullets(array $data): string {
        $title = $data['job_title'] ?? 'team member';
        return "• Led key initiatives as {$title}, driving measurable improvements in team productivity and project delivery\n• Collaborated with cross-functional teams to implement process improvements, reducing cycle time by significant margins\n• Mentored junior team members and contributed to knowledge-sharing practices across the organization";
    }

    private static function templateSkills(array $data): string {
        $role = strtolower($data['target_role'] ?? '');
        if (str_contains($role, 'developer') || str_contains($role, 'engineer')) {
            return 'REST APIs, Microservices, CI/CD, Unit Testing, Agile, System Design';
        } elseif (str_contains($role, 'designer') || str_contains($role, 'ux')) {
            return 'Design Systems, Accessibility, A/B Testing, Responsive Design, Usability Testing';
        } elseif (str_contains($role, 'data') || str_contains($role, 'analyst')) {
            return 'Statistical Analysis, Data Visualization, ETL, A/B Testing, Predictive Modeling';
        }
        return 'Project Management, Strategic Planning, Cross-functional Leadership, Data-Driven Decision Making';
    }

    private static function fallbackSuggestions(array $resume): array {
        $tips = [];
        if (empty($resume['summary'])) $tips[] = 'Add a professional summary — it is the first thing recruiters read.';
        if (strlen($resume['skills'] ?? '') < 20) $tips[] = 'Add more skills to improve ATS keyword matching.';
        if (count($resume['experience'] ?? []) < 2) $tips[] = 'Include at least 2 work experiences with quantifiable achievements.';
        if (empty($resume['education'])) $tips[] = 'Add your education to complete your resume.';
        if (empty($tips)) $tips[] = 'Your resume looks good! Consider tailoring it for each specific job application.';
        $tips[] = 'Use action verbs like "Led", "Built", "Increased" to start bullet points.';
        
        $score = 50 + min(count($resume['experience'] ?? []) * 10, 20) + (empty($resume['summary']) ? 0 : 15) + min(strlen($resume['skills'] ?? '') / 5, 15);
        
        return ['success' => true, 'suggestions' => ['score' => min((int)$score, 95), 'tips' => $tips]];
    }

    private static function parseSuggestions(string $raw): array {
        $raw = trim($raw);
        // Strip markdown code fences if present
        $raw = preg_replace('/^```json\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/i', '', $raw);
        $parsed = json_decode($raw, true);
        if ($parsed && isset($parsed['tips'])) {
            return $parsed;
        }
        return ['score' => 70, 'tips' => ['Could not parse AI response. Try again.']];
    }

    // ─── Config ───────────────────────────────────────────

    private static function getApiKey(): ?string {
        $configFile = __DIR__ . '/../../config/ai.php';
        if (file_exists($configFile)) {
            $config = include $configFile;
            if (is_array($config) && !empty($config['gemini_api_key'])) {
                return $config['gemini_api_key'];
            }
        }
        return null;
    }
}
