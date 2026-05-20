<?php
require_once __DIR__ . '/../../backend/helpers/session.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../backend/helpers/csrf.php';

// If already logged in, redirect
if (isLoggedIn()) {
    if (getCurrentUserRole() === 'employer') {
        header('Location: ' . EMPLOYER_URL . 'dashboard.php');
    } else {
        header('Location: ' . EMPLOYEE_URL . 'applications.php');
    }
    exit;
}

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Hireable</title>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,700;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/global.css">
    <link rel="stylesheet" href="../../public/assets/css/auth.css">
    <link rel="stylesheet" href="../../public/assets/css/toast.css">
</head>
<body class="auth-page">
    <div class="auth-layout">
        <!-- Left Panel: Branding -->
        <div class="auth-brand-panel">
            <div class="auth-brand-content">
                <div class="auth-brand-logo">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 2rem;">work</span>
                </div>
                <h1 class="auth-brand-title">Hireable</h1>
                <p class="auth-brand-tagline">Where exceptional talent meets extraordinary opportunity.</p>
                <div class="auth-brand-stats">
                    <div class="auth-brand-stat">
                        <span class="auth-brand-stat-value">12K+</span>
                        <span class="auth-brand-stat-label">Active Positions</span>
                    </div>
                    <div class="auth-brand-stat">
                        <span class="auth-brand-stat-value">8.5K</span>
                        <span class="auth-brand-stat-label">Companies</span>
                    </div>
                    <div class="auth-brand-stat">
                        <span class="auth-brand-stat-value">96%</span>
                        <span class="auth-brand-stat-label">Placement Rate</span>
                    </div>
                </div>
                <blockquote class="auth-brand-quote">
                    <p>"Hireable connected me with my dream role in under two weeks. The platform's curation is unmatched."</p>
                    <footer>— Sarah M., VP of Product at Lumina</footer>
                </blockquote>
            </div>
            <div class="auth-brand-gradient"></div>
        </div>

        <!-- Right Panel: Form -->
        <div class="auth-form-panel">
            <div class="auth-form-wrapper">
                <div class="auth-form-header">
                    <h2 class="auth-form-title">Welcome back</h2>
                    <p class="auth-form-subtitle">Sign in to continue your career journey</p>
                </div>

                <?php if ($flash): ?>
                    <div class="auth-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>-banner">
                        <span class="material-symbols-outlined"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span>
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>



                <form method="POST" action="/action/auth.login" class="auth-form" novalidate>
                    <?= csrfField() ?>
                    <div class="auth-field">
                        <label class="auth-label" for="email">Email Address</label>
                        <div class="auth-input-wrap">
                            <span class="material-symbols-outlined auth-input-icon">mail</span>
                            <input class="auth-input" id="email" name="email" type="email"
                                value=""
                                placeholder="you@example.com" required>
                        </div>

                    </div>

                    <div class="auth-field">
                        <div class="auth-label-row">
                            <label class="auth-label" for="password">Password</label>
                            <a class="auth-forgot-link" href="#">Forgot password?</a>
                        </div>
                        <div class="auth-input-wrap">
                            <span class="material-symbols-outlined auth-input-icon">lock</span>
                            <input class="auth-input" id="password" name="password" type="password"
                                placeholder="Enter your password" required>
                            <button type="button" class="auth-toggle-pw">
                                <span class="material-symbols-outlined" id="pw-toggle-icon">visibility</span>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <p class="auth-field-error"><?= htmlspecialchars($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="auth-remember-row">
                        <label class="auth-checkbox-label">
                            <input type="checkbox" name="remember" class="auth-checkbox">
                            <span>Remember me for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" class="auth-submit-btn">
                        Sign In
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </form>

                <div class="auth-divider"><span>or continue with</span></div>

                <div class="auth-social-row">
                    <button class="auth-social-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Google
                    </button>
                    <button class="auth-social-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#0A66C2"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        LinkedIn
                    </button>
                </div>

                <p class="auth-switch-text">
                    Don't have an account? <a href="../auth/signup.php" class="auth-switch-link">Create one</a>
                </p>
            </div>
        </div>
    </div>

    <script src="../../public/assets/js/auth.js"></script>
</body>
</html>
