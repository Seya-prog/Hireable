<?php
/**
 * Application Routes — Apply, withdraw, status updates
 */
$router->post('employee.applications.apply',     [ApplicationController::class, 'apply']);
$router->post('employee.applications.withdraw',  [ApplicationController::class, 'withdraw']);
$router->post('employer.applications.update',    [ApplicationController::class, 'updateStatus']);
