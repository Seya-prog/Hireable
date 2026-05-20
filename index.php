<?php
/**
 * Root redirect → public/index.php
 * 
 * In production, set Apache DocumentRoot to public/.
 * This file is a fallback for local development.
 */
header('Location: public/index.php');
exit;