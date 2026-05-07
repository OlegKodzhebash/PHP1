<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

requireAdmin();

$pdo = getDb();

$id = $_GET['id'] ?? null;

if ($id && filter_var($id, FILTER_VALIDATE_INT)) {
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: cars.php');
exit;