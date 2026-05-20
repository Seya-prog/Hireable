<?php
require_once __DIR__ . '/../config/database.php';

echo "Running schema migration...\n\n";

$sql = file_get_contents(__DIR__ . '/schema.sql');

// Execute as multi-query by wrapping the whole thing
// This preserves SET FOREIGN_KEY_CHECKS across statements
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

// Split by semicolons, filter empty
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    function($s) {
        $clean = trim(preg_replace('/--.*$/m', '', $s));
        return !empty($clean);
    }
);

$success = 0;
$skipped = 0;
$errors = 0;

foreach ($statements as $stmt) {
    $clean = trim(preg_replace('/--.*$/m', '', $stmt));
    if (empty($clean)) continue;
    
    // Skip SET statements (we already did FK checks)
    if (stripos($clean, 'SET FOREIGN_KEY_CHECKS') !== false) {
        $skipped++;
        continue;
    }
    
    try {
        $pdo->exec($clean);
        
        if (preg_match('/DROP TABLE.*?(\w+)$/im', $clean, $m)) {
            echo "  ✗ Dropped: {$m[1]}\n";
        } elseif (preg_match('/CREATE TABLE\s+(\w+)/i', $clean, $m)) {
            echo "  ✓ Created: {$m[1]}\n";
        } elseif (preg_match('/ALTER TABLE\s+(\w+)/i', $clean, $m)) {
            echo "  ✓ Altered: {$m[1]}\n";
        }
        $success++;
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'Duplicate column')) {
            echo "  ~ Skipped (exists): " . substr($clean, 0, 50) . "\n";
            $skipped++;
        } else {
            echo "  ✗ ERROR: " . $e->getMessage() . "\n";
            echo "    SQL: " . substr($clean, 0, 80) . "...\n\n";
            $errors++;
        }
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

echo "\n─────────────────────────────────\n";
echo "Done: $success ok, $skipped skipped, $errors errors\n\n";

// Verify
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "Tables in hireable (" . count($tables) . "):\n";
foreach ($tables as $t) {
    $cols = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='hireable' AND TABLE_NAME='$t'")->fetchColumn();
    echo "  • $t ($cols columns)\n";
}
