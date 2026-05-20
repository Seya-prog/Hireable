<?php
/**
 * JobController — Job CRUD + API endpoints
 */
class JobController {
    private JobRepository $jobs;
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->jobs = new JobRepository($pdo);
    }

    public function create(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $title = sanitize(getPost('title'));
        if (empty($title)) {
            setFlash('error', 'Job title is required.');
            header('Location: ' . EMPLOYER_URL . 'job-create.php');
            exit;
        }

        $status = getPost('status');
        if (!in_array($status, ['draft', 'active', 'closed'])) $status = 'draft';

        $this->jobs->create(getCurrentUserId(), [
            'title'                => $title,
            'department'           => sanitize(getPost('department')),
            'location'             => sanitize(getPost('location')),
            'job_type'             => sanitize(getPost('job_type')),
            'experience_level'     => sanitize(getPost('experience_level')),
            'salary_min'           => floatval(getPost('salary_min')) ?: null,
            'salary_max'           => floatval(getPost('salary_max')) ?: null,
            'description'          => sanitize(getPost('description')),
            'requirements'         => sanitize(getPost('requirements')),
            'responsibilities'     => sanitize(getPost('responsibilities')),
            'benefits'             => sanitize(getPost('benefits')),
            'skills_required'      => sanitize(getPost('skills_required')),
            'application_deadline' => getPost('application_deadline') ?: null,
            'status'               => $status,
        ]);

        setFlash('success', "Job \"$title\" created successfully!");
        header('Location: ' . EMPLOYER_URL . 'jobs.php');
        exit;
    }

    public function update(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $jobId = intval(getPost('job_id'));
        $employerId = getCurrentUserId();

        if (!$this->jobs->isOwner($jobId, $employerId)) {
            setFlash('error', 'Job not found or access denied.');
            header('Location: ' . EMPLOYER_URL . 'jobs.php');
            exit;
        }

        $title = sanitize(getPost('title'));
        if (empty($title)) {
            setFlash('error', 'Job title is required.');
            header('Location: ' . EMPLOYER_URL . "job-detail.php?id=$jobId");
            exit;
        }

        $this->jobs->update($jobId, $employerId, [
            'title'                => $title,
            'department'           => sanitize(getPost('department')),
            'location'             => sanitize(getPost('location')),
            'job_type'             => sanitize(getPost('job_type')),
            'experience_level'     => sanitize(getPost('experience_level')),
            'salary_min'           => floatval(getPost('salary_min')) ?: null,
            'salary_max'           => floatval(getPost('salary_max')) ?: null,
            'description'          => sanitize(getPost('description')),
            'requirements'         => sanitize(getPost('requirements')),
            'responsibilities'     => sanitize(getPost('responsibilities')),
            'benefits'             => sanitize(getPost('benefits')),
            'skills_required'      => sanitize(getPost('skills_required')),
            'application_deadline' => getPost('application_deadline') ?: null,
            'status'               => sanitize(getPost('status')) ?: 'draft',
        ]);

        setFlash('success', "Job \"$title\" updated successfully!");
        header('Location: ' . EMPLOYER_URL . "job-detail.php?id=$jobId");
        exit;
    }

    public function delete(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $jobId = intval(getPost('job_id'));
        $employerId = getCurrentUserId();
        $title = $this->jobs->getTitle($jobId);

        if (!$this->jobs->close($jobId, $employerId)) {
            setFlash('error', 'Job not found or access denied.');
        } else {
            setFlash('success', "Job \"$title\" has been closed.");
        }

        header('Location: ' . EMPLOYER_URL . 'jobs.php');
        exit;
    }

    // ── API Methods (return JSON) ──

    public function apiSearch(): void {
        $filters = [
            'q'        => $_GET['q'] ?? '',
            'location' => $_GET['location'] ?? '',
            'type'     => $_GET['type'] ?? '',
            'level'    => $_GET['level'] ?? '',
        ];
        $jobs = $this->jobs->findActiveJobs($filters);
        echo json_encode(['success' => true, 'data' => $jobs, 'total' => count($jobs)]);
    }

    public function apiDetail(): void {
        $id = intval($_GET['id'] ?? 0);
        $job = $this->jobs->findById($id);
        if (!$job) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Job not found']);
            return;
        }
        echo json_encode(['success' => true, 'data' => $job]);
    }

    public function apiBookmark(): void {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Login required']);
            return;
        }
        $jobId = intval($_POST['job_id'] ?? json_decode(file_get_contents('php://input'), true)['job_id'] ?? 0);
        // Bookmark logic (placeholder — table not yet created)
        echo json_encode(['success' => true, 'bookmarked' => true, 'job_id' => $jobId]);
    }

    public function apiStats(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Forbidden']);
            return;
        }
        $stats = $this->jobs->countByStatus(getCurrentUserId());
        echo json_encode(['success' => true, 'data' => $stats]);
    }
}
