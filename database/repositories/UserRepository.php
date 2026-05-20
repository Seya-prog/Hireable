<?php
/**
 * UserRepository — All user-related database queries
 */
class UserRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (first_name, last_name, email, country, password, role)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['country'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role']
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateProfile(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        return $this->pdo->prepare($sql)->execute($params);
    }

    public function updateLastLogin(int $id): void {
        $this->pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$id]);
    }

    public function getSkills(int $userId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM skills WHERE user_id = ? ORDER BY skill_name');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getExperience(int $userId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM work_experience WHERE user_id = ? ORDER BY is_current DESC, start_date DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getEducation(int $userId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM education WHERE user_id = ? ORDER BY start_date DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getCertifications(int $userId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM certifications WHERE user_id = ? ORDER BY issue_date DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
