<?php

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

$pdo = new PDO('sqlite:' . $config['db_path']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS habits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    category_id INTEGER NOT NULL,
    difficulty TEXT NOT NULL,
    start_date TEXT NOT NULL,
    goal_days INTEGER NOT NULL,
    days TEXT NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
");

$categories = [
    'Здоровье',
    'Спорт',
    'Образование',
    'Продуктивность',
    'Осознанность',
    'Социальное',
    'Финансы',
    'Другое',
];

$stmt = $pdo->prepare("INSERT OR IGNORE INTO categories (name) VALUES (:name)");

foreach ($categories as $category) {
    $stmt->execute(['name' => $category]);
}

echo 'База данных успешно создана.';