<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

use App\Database;

function getCategories(): array
{
    $pdo = Database::getConnection();

    return $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
}

function getDifficulties(): array
{
    return [
        'easy' => '🟢 Лёгкая',
        'medium' => '🟡 Средняя',
        'hard' => '🔴 Сложная',
    ];
}

function getDayLabels(): array
{
    return [
        'mon' => 'Пн',
        'tue' => 'Вт',
        'wed' => 'Ср',
        'thu' => 'Чт',
        'fri' => 'Пт',
        'sat' => 'Сб',
        'sun' => 'Вс',
    ];
}

function validateHabit(array $data): array
{
    $errors = [];

    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');
    $categoryId = (int)($data['category_id'] ?? 0);
    $difficulty = trim($data['difficulty'] ?? '');
    $startDate = trim($data['start_date'] ?? '');
    $goalDays = trim($data['goal_days'] ?? '');
    $days = $data['days'] ?? [];

    if ($name === '') {
        $errors[] = 'Название привычки обязательно.';
    } elseif (strlen($name) < 3) {
        $errors[] = 'Название должно содержать минимум 3 символа.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Название не должно превышать 100 символов.';
    }

    if (strlen($description) > 1000) {
        $errors[] = 'Описание не должно превышать 1000 символов.';
    }

    if ($categoryId <= 0) {
        $errors[] = 'Выберите категорию.';
    }

    if (!array_key_exists($difficulty, getDifficulties())) {
        $errors[] = 'Выберите корректную сложность.';
    }

    if ($startDate === '') {
        $errors[] = 'Укажите дату начала.';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $startDate);

        if (!$date || $date->format('Y-m-d') !== $startDate) {
            $errors[] = 'Дата начала имеет неверный формат.';
        }
    }

    if ($goalDays === '' || !ctype_digit($goalDays) || (int)$goalDays < 1 || (int)$goalDays > 365) {
        $errors[] = 'Цель должна быть числом от 1 до 365.';
    }

    $allowedDays = array_keys(getDayLabels());
    $days = array_values(array_filter($days, fn($day) => in_array($day, $allowedDays, true)));

    if (count($days) === 0) {
        $errors[] = 'Выберите хотя бы один день выполнения.';
    }

    return [
        $errors,
        [
            'name' => $name,
            'description' => $description,
            'category_id' => $categoryId,
            'difficulty' => $difficulty,
            'start_date' => $startDate,
            'goal_days' => (int)$goalDays,
            'days' => json_encode($days, JSON_UNESCAPED_UNICODE),
        ]
    ];
}

function createRecord(array $data): bool
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        INSERT INTO habits 
        (name, description, category_id, difficulty, start_date, goal_days, days, created_at)
        VALUES 
        (:name, :description, :category_id, :difficulty, :start_date, :goal_days, :days, :created_at)
    ");

    return $stmt->execute([
        'name' => $data['name'],
        'description' => $data['description'],
        'category_id' => $data['category_id'],
        'difficulty' => $data['difficulty'],
        'start_date' => $data['start_date'],
        'goal_days' => $data['goal_days'],
        'days' => $data['days'],
        'created_at' => date('Y-m-d H:i:s'),
    ]);
}

function getAllRecords(): array
{
    $pdo = Database::getConnection();

    $stmt = $pdo->query("
        SELECT habits.*, categories.name AS category_name
        FROM habits
        JOIN categories ON habits.category_id = categories.id
        ORDER BY habits.created_at DESC
    ");

    $records = $stmt->fetchAll();

    foreach ($records as &$record) {
        $record['days'] = json_decode($record['days'], true) ?: [];
    }

    return $records;
}

function getRecordById(int $id): ?array
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT * FROM habits
        WHERE id = :id
    ");

    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch();

    if (!$record) {
        return null;
    }

    $record['days'] = json_decode($record['days'], true) ?: [];

    return $record;
}

function updateRecord(int $id, array $data): bool
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        UPDATE habits
        SET 
            name = :name,
            description = :description,
            category_id = :category_id,
            difficulty = :difficulty,
            start_date = :start_date,
            goal_days = :goal_days,
            days = :days
        WHERE id = :id
    ");

    return $stmt->execute([
        'id' => $id,
        'name' => $data['name'],
        'description' => $data['description'],
        'category_id' => $data['category_id'],
        'difficulty' => $data['difficulty'],
        'start_date' => $data['start_date'],
        'goal_days' => $data['goal_days'],
        'days' => $data['days'],
    ]);
}

function deleteRecord(int $id): bool
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("DELETE FROM habits WHERE id = :id");

    return $stmt->execute(['id' => $id]);
}

function searchRecords(string $query): array
{
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT habits.*, categories.name AS category_name
        FROM habits
        JOIN categories ON habits.category_id = categories.id
        WHERE habits.name LIKE :query 
           OR habits.description LIKE :query
           OR categories.name LIKE :query
        ORDER BY habits.created_at DESC
    ");

    $stmt->execute([
        'query' => '%' . $query . '%',
    ]);

    $records = $stmt->fetchAll();

    foreach ($records as &$record) {
        $record['days'] = json_decode($record['days'], true) ?: [];
    }

    return $records;
}