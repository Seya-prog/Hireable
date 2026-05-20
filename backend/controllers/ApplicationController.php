<?php
/**
 * ApplicationController — Apply, Withdraw, Status Updates + API
 */
class ApplicationController {
    private ApplicationRepository $apps;
    private JobRepository $jobs;

    public function __construct(PDO $pdo) {
        $this->apps = new ApplicationRepository($pdo);
        $this->jobs = new JobRepository($pdo);
    }

    public function apply(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $employeeId = getCurrentUserId();
        $jobId = intval(getPost('job_id'));
        $coverLetter = sanitize(getPost('cover_letter'));

        // Verify job is active
        $job = $this->jobs->findById($jobId);
        if (!$job || $job['status'] !== 'active') {
            setFlash('error', 'This job is no longer accepting applications.');
            header('Location: ' . EMPLOYEE_URL . 'job-search.php');
            exit;
        }

        if ($this->apps->isDuplicate($jobId, $employeeId)) {
            setFlash('error', 'You have already applied to this position.');
            header('Location: ' . EMPLOYEE_URL . 'applications.php');
            exit;
        }

        // Handle resume upload
        $resumePath = null;
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_DIR . '/assets/uploads/resumes/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
            $filename = 'resume_' . $employeeId . '_' . $jobId . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['resume']['tmp_name'], $uploadDir . $filename);
            $resumePath = 'assets/uploads/resumes/' . $filename;
        }

        $this->apps->apply($jobId, $employeeId, [
            'cover_letter' => $coverLetter,
            'resume_path'  => $resumePath,
        ]);

        setFlash('success', 'Applied to "' . $job['title'] . '" successfully!');
        header('Location: ' . EMPLOYEE_URL . 'applications.php');
        exit;
    }

    public function withdraw(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employee') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $applicationId = intval(getPost('application_id'));
        $app = $this->apps->findById($applicationId);

        if (!$app) {
            setFlash('error', 'Application not found.');
        } else {
            $this->apps->withdraw($applicationId, getCurrentUserId());
            setFlash('success', 'Application to "' . $app['job_title'] . '" withdrawn.');
        }

        header('Location: ' . EMPLOYEE_URL . 'applications.php');
        exit;
    }

    public function updateStatus(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $applicationId = intval(getPost('application_id'));
        $newStatus = getPost('status');
        $notes = getPost('notes');

        $validStatuses = ['applied', 'screening', 'interview', 'offer', 'rejected'];
        if (!in_array($newStatus, $validStatuses)) {
            setFlash('error', 'Invalid status.');
            header('Location: ' . EMPLOYER_URL . 'candidates.php');
            exit;
        }

        $app = $this->apps->findForEmployer($applicationId, getCurrentUserId());
        if (!$app) {
            setFlash('error', 'Application not found.');
        } else {
            $this->apps->updateStatus($applicationId, $newStatus, $notes);
            $name = $app['first_name'] . ' ' . $app['last_name'];
            setFlash('success', "$name moved to " . ucfirst($newStatus) . " stage.");
        }

        header('Location: ' . EMPLOYER_URL . 'candidates.php');
        exit;
    }

    // ── API ──

    public function apiStatus(): void {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Login required']);
            return;
        }
        $counts = $this->apps->countByEmployee(getCurrentUserId());
        echo json_encode(['success' => true, 'data' => $counts]);
    }
}
