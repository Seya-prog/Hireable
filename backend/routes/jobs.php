<?php
/**
 * Job Routes — All job-related actions (employer + employee)
 */
$router->post('employer.jobs.create',  [JobController::class, 'create']);
$router->post('employer.jobs.update',  [JobController::class, 'update']);
$router->post('employer.jobs.delete',  [JobController::class, 'delete']);
