<?php
/**
 * InterviewRepository — All interview-related database queries
 */
class InterviewRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(array $data): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO interviews (application_id, employer_id, employee_id, scheduled_date, start_time,
                                    duration_minutes, interview_type, meeting_link, location,
                                    panel_members, notes_for_candidate)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['application_id'], $data['employer_id'], $data['employee_id'],
            $data['scheduled_date'], $data['start_time'], $data['duration_minutes'] ?? 60,
            $data['interview_type'] ?? null, $data['meeting_link'] ?? null,
            $data['location'] ?? null, $data['panel_members'] ?? null,
            $data['notes_for_candidate'] ?? null
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function findByEmployer(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, u.first_name, u.last_name, j.title AS job_title
             FROM interviews i
             JOIN applications a ON i.application_id = a.id
             JOIN users u ON i.employee_id = u.id
             JOIN jobs j ON a.job_id = j.id
             WHERE i.employer_id = ?
             ORDER BY i.scheduled_date ASC, i.start_time ASC'
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll();
    }

    public function findByEmployee(int $employeeId): array {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, u.company_name, j.title AS job_title
             FROM interviews i
             JOIN users u ON i.employer_id = u.id
             JOIN applications a ON i.application_id = a.id
             JOIN jobs j ON a.job_id = j.id
             WHERE i.employee_id = ?
             ORDER BY i.scheduled_date ASC'
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM interviews WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getEmployeeFromApplication(int $applicationId): ?array {
        $stmt = $this->pdo->prepare(
            'SELECT a.employee_id, u.first_name, u.last_name
             FROM applications a JOIN users u ON a.employee_id = u.id
             WHERE a.id = ?'
        );
        $stmt->execute([$applicationId]);
        return $stmt->fetch() ?: null;
    }

    public function markCompleted(int $id): bool {
        return $this->pdo->prepare('UPDATE interviews SET status = "completed" WHERE id = ?')->execute([$id]);
    }

    public function addFeedback(int $interviewId, int $reviewerId, array $data): bool {
        $stmt = $this->pdo->prepare(
            'INSERT INTO interview_feedback (interview_id, reviewer_id, overall_rating, technical_rating,
                        communication_rating, problem_solving_rating, culture_fit_rating,
                        strengths, improvements, additional_notes, recommendation)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE overall_rating=VALUES(overall_rating), technical_rating=VALUES(technical_rating),
                        communication_rating=VALUES(communication_rating), problem_solving_rating=VALUES(problem_solving_rating),
                        culture_fit_rating=VALUES(culture_fit_rating), strengths=VALUES(strengths),
                        improvements=VALUES(improvements), additional_notes=VALUES(additional_notes),
                        recommendation=VALUES(recommendation)'
        );
        return $stmt->execute([
            $interviewId, $reviewerId,
            $data['overall_rating'], $data['technical_rating'],
            $data['communication_rating'], $data['problem_solving_rating'],
            $data['culture_fit_rating'], $data['strengths'] ?? null,
            $data['improvements'] ?? null, $data['additional_notes'] ?? null,
            $data['recommendation'] ?? null
        ]);
    }

    /**
     * Find upcoming interviews for an employer (today and future)
     */
    public function findUpcoming(int $employerId, int $limit = 5): array {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, u.first_name, u.last_name, j.title AS job_title
             FROM interviews i
             JOIN applications a ON i.application_id = a.id
             JOIN users u ON i.employee_id = u.id
             JOIN jobs j ON a.job_id = j.id
             WHERE i.employer_id = ? AND i.scheduled_date >= CURDATE() AND i.status != "completed"
             ORDER BY i.scheduled_date ASC, i.start_time ASC
             LIMIT ' . intval($limit)
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll();
    }

    /**
     * Count interviews this week by status
     */
    public function countThisWeek(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = "scheduled" THEN 1 ELSE 0 END) as scheduled
             FROM interviews
             WHERE employer_id = ?
               AND scheduled_date BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                                      AND DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY)'
        );
        $stmt->execute([$employerId]);
        return $stmt->fetch() ?: ['total' => 0, 'completed' => 0, 'scheduled' => 0];
    }

    /**
     * Find interviews pending feedback
     */
    public function findPendingFeedback(int $employerId): array {
        $stmt = $this->pdo->prepare(
            'SELECT i.*, u.first_name, u.last_name, j.title AS job_title
             FROM interviews i
             JOIN applications a ON i.application_id = a.id
             JOIN users u ON i.employee_id = u.id
             JOIN jobs j ON a.job_id = j.id
             LEFT JOIN interview_feedback f ON f.interview_id = i.id
             WHERE i.employer_id = ? AND i.status = "completed" AND f.id IS NULL
             ORDER BY i.scheduled_date DESC'
        );
        $stmt->execute([$employerId]);
        return $stmt->fetchAll();
    }

    /**
     * Find interviews grouped by date for employer
     */
    public function findByEmployerGrouped(int $employerId): array {
        $rows = $this->findByEmployer($employerId);
        $grouped = [];
        foreach ($rows as $row) {
            $date = $row['scheduled_date'];
            $grouped[$date][] = $row;
        }
        return $grouped;
    }
}
