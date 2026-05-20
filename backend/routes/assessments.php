<?php
/**
 * Assessment Routes — Create, update, start, and submit
 */
$router->post('employer.assessments.create',   [AssessmentController::class, 'create']);
$router->post('employer.assessments.update',   [AssessmentController::class, 'update']);
$router->post('employee.assessments.submit',   [AssessmentController::class, 'submit']);
