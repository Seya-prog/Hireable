<?php
/**
 * Interview Routes — Scheduling and feedback
 */
$router->post('employer.interviews.create',    [InterviewController::class, 'create']);
$router->post('employer.interviews.feedback',  [InterviewController::class, 'feedback']);
