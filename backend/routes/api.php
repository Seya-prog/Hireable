<?php
/**
 * API Routes — JSON endpoints for AJAX requests
 */
$router->get('jobs/search',         [JobController::class, 'apiSearch']);
$router->get('jobs/detail',         [JobController::class, 'apiDetail']);
$router->post('jobs/bookmark',      [JobController::class, 'apiBookmark']);
$router->get('employer/stats',      [JobController::class, 'apiStats']);
$router->get('applications/status', [ApplicationController::class, 'apiStatus']);

// Assessment proctoring API
$router->post('assessment/start',       [AssessmentController::class, 'apiStart']);
$router->post('assessment/save-answer', [AssessmentController::class, 'apiSaveAnswer']);
$router->post('assessment/heartbeat',   [AssessmentController::class, 'apiHeartbeat']);
$router->post('assessment/snapshot',    [AssessmentController::class, 'apiSnapshot']);
$router->post('assessment/submit',      [AssessmentController::class, 'apiSubmit']);

// Resume AI API
$router->post('resume/enhance',      [ResumeController::class, 'apiEnhance']);
$router->post('resume/suggestions',  [ResumeController::class, 'apiSuggestions']);
