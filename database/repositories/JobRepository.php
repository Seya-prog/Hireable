<?php
/**
 * JobRepository — All job-related database queries
 */
class JobRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Search active jobs with optional filters
     */
    public function findActiveJobs(array $filters = []): array {
        $sql = "SELECT j.*, u.first_name, u.last_name, u.company_name
                FROM jobs j JOIN users u ON j.employer_id = u.id
                WHERE j.status = 'active'";
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR u.company_name LIKE ?)";
            $s = '%' . $filters['q'] . '%';
            array_push($params, $s, $s, $s);
        }
        if (!empty($filters['location'])) {
            $sql .= " AND j.location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }
        if (!empty($filters['type'])) {
            $sql .= " AND j.job_type = ?";
            $params[] = $filters['type'];
        }
        if (!empty($filters['level'])) {
            $sql .= " AND j.experience_level = ?";
            $params[] = $filters['level'];
        }

        $sql .= " ORDER BY j.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * All jobs for an employer (any status)
     */
    public function findByEmployer(int $employerId, ?string $status = null): array {
        $sql = "SELECT j.*, (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) AS applicant_count
                FROM jobs j WHERE j.employer_id = ?";
        $params = [$employerId];

        if ($status) {
            $sql .= " AND j.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY j.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT j.*, u.first_name, u.last_name, u.company_name
             FROM jobs j JOIN users u ON j.employer_id = u.id
             WHERE j.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(int $employerId, array $data): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO jobs (employer_id, title, department, location, job_type, experience_level,
                              salary_min, salary_max, description, requirements, responsibilities,
                              benefits, skills_required, application_deadline, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $employerId, $data['title'], $data['department'] ?? null, $data['location'] ?? null,
            $data['job_type'] ?? 'full-time', $data['experience_level'] ?? 'mid',
            $data['salary_min'] ?? null, $data['salary_max'] ?? null,
            $data['description'] ?? null, $data['requirements'] ?? null,
            $data['responsibilities'] ?? null, $data['benefits'] ?? null,
            $data['skills_required'] ?? null, $data['application_deadline'] ?? null,
            $data['status'] ?? 'draft'
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, int $employerId, array $data): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE jobs SET title=?, department=?, location=?, job_type=?, experience_level=?,
                            salary_min=?, salary_max=?, description=?, requirements=?,
                            responsibilities=?, benefits=?, skills_required=?,
                            application_deadline=?, status=?
             WHERE id=? AND employer_id=?'
        );
        return $stmt->execute([
            $data['title'], $data['department'] ?? null, $data['location'] ?? null,
            $data['job_type'] ?? 'full-time', $data['experience_level'] ?? 'mid',
            $data['salary_min'] ?? null, $data['salary_max'] ?? null,
            $data['description'] ?? null, $data['requirements'] ?? null,
            $data['responsibilities'] ?? null, $data['benefits'] ?? null,
            $data['skills_required'] ?? null, $data['application_deadline'] ?? null,
            $data['status'] ?? 'draft',
            $id, $employerId
        ]);
    }

    public function close(int $id, int $employerId): bool {
        $stmt = $this->pdo->prepare('UPDATE jobs SET status = "closed" WHERE id = ? AND employer_id = ?');
        return $stmt->execute([$id, $employerId]);
    }

    /**
     * Get counts by status for dashboard stats
     */
    public function countByStatus(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT status, COUNT(*) as count FROM jobs WHERE employer_id = ? GROUP BY status'
        );
        $stmt->execute([$employerId]);
        $counts = ['active' => 0, 'draft' => 0, 'closed' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }
        $counts['total'] = array_sum($counts);
        return $counts;
    }

    /**
     * Verify a job belongs to an employer
     */
    public function isOwner(int $jobId, int $employerId): bool {
        $stmt = $this->pdo->prepare('SELECT id FROM jobs WHERE id = ? AND employer_id = ?');
        $stmt->execute([$jobId, $employerId]);
        return (bool) $stmt->fetch();
    }

    /**
     * Get job title by ID
     */
    public function getTitle(int $id): ?string {
        $stmt = $this->pdo->prepare('SELECT title FROM jobs WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $row['title'] : null;
    }
}
