<?php
/**
 * AuthController — Login, Signup, Logout
 */
class AuthController {
    private UserRepository $users;

    public function __construct(PDO $pdo) {
        $this->users = new UserRepository($pdo);
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $email = sanitize(getPost('email'));
        $password = getPost('password');

        if (empty($email) || empty($password)) {
            setFlash('error', 'Please enter your email and password.');
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $user = $this->users->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            setUserSession($user);
            $this->users->updateLastLogin($user['id']);
            setFlash('success', 'Welcome back, ' . $user['first_name'] . '!');

            if ($user['role'] === 'employer') {
                header('Location: ' . EMPLOYER_URL . 'dashboard.php');
            } else {
                header('Location: ' . EMPLOYEE_URL . 'applications.php');
            }
            exit;
        }

        setFlash('error', 'Invalid email or password.');
        header('Location: ' . AUTH_URL . 'login.php');
        exit;
    }

    public function signup(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . AUTH_URL . 'signup.php');
            exit;
        }

        $role      = getPost('role');
        $firstName = sanitize(getPost('first_name'));
        $lastName  = sanitize(getPost('last_name'));
        $email     = sanitize(getPost('email'));
        $country   = getPost('country');
        $password  = getPost('password');

        // Validate
        if (empty($firstName) || empty($lastName)) {
            setFlash('error', 'Please enter your full name.');
            header('Location: ' . AUTH_URL . 'signup.php');
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            header('Location: ' . AUTH_URL . 'signup.php');
            exit;
        }
        if (strlen($password) < 8) {
            setFlash('error', 'Password must be at least 8 characters.');
            header('Location: ' . AUTH_URL . 'signup.php');
            exit;
        }
        if (!in_array($role, ['employee', 'employer'])) {
            setFlash('error', 'Invalid account type.');
            header('Location: ' . AUTH_URL . 'signup.php');
            exit;
        }

        // Check duplicate
        if ($this->users->findByEmail($email)) {
            setFlash('error', 'An account with this email already exists.');
            header('Location: ' . AUTH_URL . 'signup.php');
            exit;
        }

        $userId = $this->users->create([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'country'    => $country,
            'password'   => $password,
            'role'       => $role,
        ]);

        $user = $this->users->findById($userId);
        setUserSession($user);
        $this->users->updateLastLogin($userId);
        setFlash('success', 'Welcome to Hireable, ' . $firstName . '!');

        if ($role === 'employer') {
            header('Location: ' . EMPLOYER_URL . 'dashboard.php');
        } else {
            header('Location: ' . EMPLOYEE_URL . 'applications.php');
        }
        exit;
    }

    public function logout(): void {
        destroySession();
        setFlash('success', 'You have been signed out.');
        header('Location: ' . AUTH_URL . 'login.php');
        exit;
    }
}
