<?php
/**
 * ProfileController — Update user profile
 */
class ProfileController {
    private UserRepository $users;

    public function __construct(PDO $pdo) {
        $this->users = new UserRepository($pdo);
    }

    public function update(): void {
        if (!isLoggedIn()) {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $userId = getCurrentUserId();
        $role = getCurrentUserRole();

        $firstName = sanitize(getPost('first_name'));
        $lastName  = sanitize(getPost('last_name'));

        if (empty($firstName) || empty($lastName)) {
            setFlash('error', 'First name and last name are required.');
            header('Location: ' . EMPLOYEE_URL . 'profile.php');
            exit;
        }

        $data = [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'phone'      => sanitize(getPost('phone')),
            'headline'   => sanitize(getPost('headline')),
            'bio'        => sanitize(getPost('bio')),
            'country'    => sanitize(getPost('country')),
            'city'       => sanitize(getPost('city')),
        ];

        // Handle profile photo upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_DIR . '/assets/uploads/profile/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $filename);
            $data['profile_photo'] = 'assets/uploads/profile/' . $filename;
        }

        // Role-specific fields
        if ($role === 'employee') {
            $data['linkedin_url']        = sanitize(getPost('linkedin_url'));
            $data['portfolio_url']       = sanitize(getPost('portfolio_url'));
            $data['github_url']          = sanitize(getPost('github_url'));
            $data['years_of_experience'] = intval(getPost('years_of_experience'));
            $data['expected_salary']     = floatval(getPost('expected_salary')) ?: null;
            $data['availability']        = sanitize(getPost('availability'));
            $data['job_preference']      = sanitize(getPost('job_preference'));
        } else {
            $data['company_name']        = sanitize(getPost('company_name'));
            $data['company_industry']    = sanitize(getPost('company_industry'));
            $data['company_size']        = sanitize(getPost('company_size'));
            $data['company_website']     = sanitize(getPost('company_website'));
            $data['company_description'] = sanitize(getPost('company_description'));
            $data['company_location']    = sanitize(getPost('company_location'));
        }

        $this->users->updateProfile($userId, $data);

        $_SESSION['user_first_name'] = $firstName;
        $_SESSION['user_last_name'] = $lastName;

        setFlash('success', 'Profile updated successfully!');

        if ($role === 'employer') {
            header('Location: ' . EMPLOYER_URL . 'dashboard.php');
        } else {
            header('Location: ' . EMPLOYEE_URL . 'profile.php');
        }
        exit;
    }
}
