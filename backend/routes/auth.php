<?php
/**
 * Auth Routes
 */
$router->post('auth.login',  [AuthController::class, 'login']);
$router->post('auth.signup', [AuthController::class, 'signup']);
$router->get('auth.logout',  [AuthController::class, 'logout']);
