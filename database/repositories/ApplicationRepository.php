<?php
/**
 * ApplicationRepository — All application-related database queries
 */
class ApplicationRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByEmployee(int $employeeId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, j.title AS job_title, j.location, j.job_type,
                    u.company_name, u.company_logo, u.first_name AS emp_first, u.last_name AS emp_last
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             JOIN users u ON j.employer_id = u.id
             WHERE a.employee_id = ?
             ORDER BY a.applied_at DESC'
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }

    public function findByJob(int $jobId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, u.first_name, u.last_name, u.email, u.headline, u.profile_photo,
                    a.resume_path, a.cover_letter
             FROM applications a
             JOIN users u ON a.employee_id = u.id
             WHERE a.job_id = ?
             ORDER BY a.applied_at DESC'
        );
        $stmt->execute([$jobId]);
        return $stmt->fetchAll();
    }

    public function findByEmployer(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, j.title AS job_title, u.first_name, u.last_name, u.email
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             JOIN users u ON a.employee_id = u.id
             WHERE j.employer_id = ?
             ORDER BY a.applied_at DESC'
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, a.resume_path, a.cover_letter, j.title AS job_title, u.first_name, u.last_name
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             JOIN users u ON a.employee_id = u.id
             WHERE a.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function isDuplicate(int $jobId, int $employeeId): bool {
        $stmt = $this->pdo->prepare('SELECT id FROM applications WHERE job_id = ? AND employee_id = ?');
        $stmt->execute([$jobId, $employeeId]);
        return (bool) $stmt->fetch();
    }

    public function apply(int $jobId, int $employeeId, array $data): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO applications (job_id, employee_id, cover_letter, resume_path, status)
             VALUES (?, ?, ?, ?, "applied")'
        );
        $stmt->execute([$jobId, $employeeId, $data['cover_letter'] ?? null, $data['resume_path'] ?? null]);
        return (int) $this->pdo->lastInsertId();
    }

    public function withdraw(int $id, int $employeeId): bool {
        $stmt = $this->pdo->prepare('UPDATE applications SET status = "withdrawn" WHERE id = ? AND employee_id = ?');
        return $stmt->execute([$id, $employeeId]);
    }

    public function updateStatus(int $id, string $status, string $notes = ''): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE applications SET status = ?, notes = CONCAT(IFNULL(notes,""), "\n", ?) WHERE id = ?'
        );
        return $stmt->execute([$status, date('Y-m-d') . ": Status → $status. $notes", $id]);
    }

    /**
     * Verify application belongs to a job owned by this employer
     */
    public function findForEmployer(int $applicationId, int $employerId): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT a.id, u.first_name, u.last_name
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             JOIN users u ON a.employee_id = u.id
             WHERE a.id = ? AND j.employer_id = ?'
        );
        $stmt->execute([$applicationId, $employerId]);
        return $stmt->fetch() ?: null;
    }

    public function countByEmployee(int $employeeId): array {
        $stmt = $this->pdo->prepare(
            'SELECT status, COUNT(*) as count FROM applications WHERE employee_id = ? GROUP BY status'
        );
        $stmt->execute([$employeeId]);
        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }
        return $counts;
    }

    /**
     * Count applications by status for all jobs owned by an employer
     */
    public function countByStatusForEmployer(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.status, COUNT(*) as count
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             WHERE j.employer_id = ?
             GROUP BY a.status'
        );
        $stmt->execute([$employerId]);
        $counts = ['applied' => 0, 'screening' => 0, 'interview' => 0, 'offer' => 0, 'rejected' => 0, 'withdrawn' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }
        $counts['total'] = array_sum($counts);
        return $counts;
    }

    /**
     * Recent applicants for an employer with limit
     */
    public function findRecentByEmployer(int $employerId, int $limit = 5): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, j.title AS job_title, u.first_name, u.last_name, u.email, u.profile_photo
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             JOIN users u ON a.employee_id = u.id
             WHERE j.employer_id = ?
             ORDER BY a.applied_at DESC
             LIMIT ' . intval($limit)
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll();
    }

    /**
     * Full applicant details with skills for employer candidate view
     */
    public function findDetailedByEmployer(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, a.resume_path, a.cover_letter, j.title AS job_title,
                    u.first_name, u.last_name, u.email,
                    u.headline, u.profile_photo, u.phone, u.city, u.country,
                    u.years_of_experience, u.linkedin_url, u.portfolio_url, u.github_url
             FROM applications a
             JOIN jobs j ON a.job_id = j.id
             JOIN users u ON a.employee_id = u.id
             WHERE j.employer_id = ?
             ORDER BY a.applied_at DESC'
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll();
    }
}
