<?php
session_start();
$errors = [];
$role = 'employee';
$firstName = '';
$lastName = '';
$email = '';
$country = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $role = $_POST['role'] ?? '';
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $country = $_POST['country'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate each field
    if (empty($firstName)) {
        $errors['first_name'] = "Please enter your first name.";
    }
    if (empty($lastName)) {
        $errors['last_name'] = "Please enter your last name.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }
    if (empty($country)) {
        $errors['country'] = "Please select your country.";
    }
    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    }
    if (empty($_POST['terms'])) {
        $errors['terms'] = "You must agree to the terms and conditions.";
    }
    if (!in_array($role, ['employee', 'employer'])) {
        $errors['role'] = "Invalid account type.";
    }

    // If no errors → process signup
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;

        require_once __DIR__ . '/../config/database.php';
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = "Email already exists.";
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users(first_name, last_name, email, country, password, role)VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $country, $hashedPassword, $role,]);

            $_SESSION['user_id'] = $pdo->lastInsertId();

            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;

            header("Location:profile.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Hireable</title>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,700;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
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
                    <p>"The best career decision I ever made was creating my profile on Hireable."</p>
                    <footer>— Daniel K., Director of Engineering</footer>
                </blockquote>
            </div>
            <div class="auth-brand-gradient"></div>
        </div>

        <!-- Right Panel: Form -->
        <div class="auth-form-panel">
            <div class="auth-form-wrapper">
                <div class="auth-form-header">
                    <h2 class="auth-form-title">Create your account</h2>
                    <p class="auth-form-subtitle">Start your journey to your next great role</p>
                </div>

                <form method="POST" action="signup.php" class="auth-form" novalidate>
                    <!-- Role Toggle -->
                    <div class="auth-field">
                        <label class="auth-label">I am here to</label>
                        <div class="auth-role-toggle">
                            <input type="radio" id="role-employee" name="role" value="employee"
                                <?= $role === 'employee' ? 'checked' : '' ?> class="auth-role-radio">
                            <label for="role-employee" class="auth-role-option">
                                <span class="material-symbols-outlined">person_search</span>
                                Apply for Jobs
                            </label>
                            <input type="radio" id="role-employer" name="role" value="employer"
                                <?= $role === 'employer' ? 'checked' : '' ?> class="auth-role-radio">
                            <label for="role-employer" class="auth-role-option">
                                <span class="material-symbols-outlined">business</span>
                                Post Jobs
                            </label>
                        </div>
                        <?php if (isset($errors['role'])): ?>
                            <p class="auth-field-error"><?= htmlspecialchars($errors['role']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Name Row -->
                    <div class="auth-field-row">
                        <div class="auth-field">
                            <label class="auth-label" for="first-name">First Name</label>
                            <div class="auth-input-wrap">
                                <input class="auth-input" id="first-name" name="first_name" type="text"
                                    value="<?= htmlspecialchars($firstName) ?>" placeholder="John" required>
                            </div>
                            <?php if (isset($errors['first_name'])): ?>
                                <p class="auth-field-error"><?= htmlspecialchars($errors['first_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label" for="last-name">Last Name</label>
                            <div class="auth-input-wrap">
                                <input class="auth-input" id="last-name" name="last_name" type="text"
                                    value="<?= htmlspecialchars($lastName) ?>" placeholder="Doe" required>
                            </div>
                            <?php if (isset($errors['last_name'])): ?>
                                <p class="auth-field-error"><?= htmlspecialchars($errors['last_name']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="auth-field">
                        <label class="auth-label" for="email">
                            <span class="role-copy employee-copy">Email Address</span>
                            <span class="role-copy employer-copy">Work Email</span>
                        </label>
                        <div class="auth-input-wrap">
                            <span class="material-symbols-outlined auth-input-icon">mail</span>
                            <input class="auth-input" id="email" name="email" type="email"
                                value="<?= htmlspecialchars($email) ?>" placeholder="you@example.com" required>
                        </div>
                        <?php if (isset($errors['email'])): ?>
                            <p class="auth-field-error"><?= htmlspecialchars($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Country -->
                    <div class="auth-field">
                        <label class="auth-label" for="country">Country</label>
                        <div class="auth-input-wrap">
                            <span class="material-symbols-outlined auth-input-icon">public</span>
                            <select class="auth-input auth-select" id="country" name="country" required>
                                <option value="" disabled <?= empty($country) ? 'selected' : '' ?>>Select country</option>
                                <option value="Ethiopia" <?= $country === 'Ethiopia' ? 'selected' : '' ?>>Ethiopia</option>
                                <option value="Kenya" <?= $country === 'Kenya' ? 'selected' : '' ?>>Kenya</option>
                                <option value="Rwanda" <?= $country === 'Rwanda' ? 'selected' : '' ?>>Rwanda</option>
                                <option value="Uganda" <?= $country === 'Uganda' ? 'selected' : '' ?>>Uganda</option>
                                <option value="Tanzania" <?= $country === 'Tanzania' ? 'selected' : '' ?>>Tanzania</option>
                                <option value="Nigeria" <?= $country === 'Nigeria' ? 'selected' : '' ?>>Nigeria</option>
                                <option value="South Africa" <?= $country === 'South Africa' ? 'selected' : '' ?>>South Africa</option>
                                <option value="Ghana" <?= $country === 'Ghana' ? 'selected' : '' ?>>Ghana</option>
                                <option value="Egypt" <?= $country === 'Egypt' ? 'selected' : '' ?>>Egypt</option>
                            </select>
                        </div>
                        <?php if (isset($errors['country'])): ?>
                            <p class="auth-field-error"><?= htmlspecialchars($errors['country']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="auth-field">
                        <label class="auth-label" for="password">Password</label>
                        <div class="auth-input-wrap">
                            <span class="material-symbols-outlined auth-input-icon">lock</span>
                            <input class="auth-input" id="password" name="password" type="password"
                                placeholder="Min. 8 characters" minlength="8" required>
                            <button type="button" class="auth-toggle-pw" onclick="togglePassword()">
                                <span class="material-symbols-outlined" id="pw-toggle-icon">visibility</span>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <p class="auth-field-error"><?= htmlspecialchars($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Terms -->
                    <div class="auth-remember-row">
                        <label class="auth-checkbox-label">
                            <input type="checkbox" name="terms" class="auth-checkbox" required>
                            <span>I agree to the <a href="#" class="auth-terms-link">Terms of Service</a> and <a href="#" class="auth-terms-link">Privacy Policy</a></span>
                        </label>
                        <?php if (isset($errors['terms'])): ?>
                            <p class="auth-field-error"><?= htmlspecialchars($errors['terms']) ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="auth-submit-btn">
                        Create Account
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </form>

                <div class="auth-divider"><span>or sign up with</span></div>

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
                    Already have an account? <a href="login.php" class="auth-switch-link">Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <script>
    function togglePassword() {
        const pw = document.getElementById('password');
        const icon = document.getElementById('pw-toggle-icon');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            pw.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    // Role toggle dynamic copy
    const radios = document.querySelectorAll('.auth-role-radio');
    radios.forEach(r => r.addEventListener('change', () => {
        const isEmployer = document.getElementById('role-employer').checked;
        document.querySelectorAll('.employee-copy').forEach(el => el.style.display = isEmployer ? 'none' : 'inline');
        document.querySelectorAll('.employer-copy').forEach(el => el.style.display = isEmployer ? 'inline' : 'none');
    }));
    </script>
</body>
</html>