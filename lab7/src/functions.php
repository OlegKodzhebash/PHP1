<?php

declare(strict_types=1);

function dataFile(): string
{
    return __DIR__ . '/../data.json';
}

function loadRecords(): array
{
    $file = dataFile();

    if (!file_exists($file)) {
        return [];
    }

    $raw = file_get_contents($file);
    return json_decode($raw, true) ?: [];
}

function saveRecords(array $records): bool
{
    return file_put_contents(
        dataFile(),
        json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    ) !== false;
}

function getCategoryLabels(): array
{
    return [
        'health' => '🏃 Здоровье',
        'sport' => '💪 Спорт',
        'education' => '📚 Образование',
        'productivity' => '⚡ Продуктивность',
        'mindfulness' => '🧘 Осознанность',
        'social' => '🤝 Социальное',
        'finance' => '💰 Финансы',
        'other' => '🔖 Другое',
    ];
}

function getDifficultyLabels(): array
{
    return [
        'easy' => '🟢 Лёгкая',
        'medium' => '🟡 Средняя',
        'hard' => '🔴 Сложная',
    ];
}

function getDifficultyColors(): array
{
    return [
        'easy' => '#d4efdf',
        'medium' => '#fef9e7',
        'hard' => '#fadbd8',
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

function getSortField(): string
{
    $allowed = ['name', 'category', 'difficulty', 'start_date', 'goal_days', 'created_at'];
    $sort = $_GET['sort'] ?? 'created_at';

    return in_array($sort, $allowed, true) ? $sort : 'created_at';
}

function getSortOrder(): string
{
    return ($_GET['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
}

function sortRecords(array &$records, string $sort, string $order): void
{
    usort($records, function ($a, $b) use ($sort, $order) {
        $va = $a[$sort] ?? '';
        $vb = $b[$sort] ?? '';

        if ($sort === 'goal_days') {
            $cmp = (int)$va <=> (int)$vb;
        } else {
            $cmp = strcmp((string)$va, (string)$vb);
        }

        return $order === 'asc' ? $cmp : -$cmp;
    });
}

function sortUrl(string $field, string $currentSort, string $currentOrder, string $view = 'native'): string
{
    $newOrder = ($field === $currentSort && $currentOrder === 'asc') ? 'desc' : 'asc';

    return 'index.php?page=list&view=' . urlencode($view)
        . '&sort=' . urlencode($field)
        . '&order=' . urlencode($newOrder);
}

function sortArrow(string $field, string $currentSort, string $currentOrder): string
{
    if ($field !== $currentSort) {
        return '↕';
    }

    return $currentOrder === 'asc' ? '▲' : '▼';
}

function safeTrimText(string $text, int $max = 80, string $suffix = '…'): string
{
    $text = trim($text);

    if ($text === '') {
        return '';
    }

    preg_match_all('/./us', $text, $matches);
    $chars = $matches[0];

    if (count($chars) <= $max) {
        return $text;
    }

    return implode('', array_slice($chars, 0, $max)) . $suffix;
}

function getAverageGoal(array $records): int
{
    if (count($records) === 0) {
        return 0;
    }

    return (int)round(array_sum(array_column($records, 'goal_days')) / count($records));
}

function validateHabit(array $post): array
{
    $errors = [];

    $name = trim($post['name'] ?? '');
    $description = trim($post['description'] ?? '');
    $category = trim($post['category'] ?? '');
    $difficulty = trim($post['difficulty'] ?? '');
    $startDate = trim($post['start_date'] ?? '');
    $goalDays = trim($post['goal_days'] ?? '');
    $days = $post['days'] ?? [];

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

    $allowedCategories = array_keys(getCategoryLabels());

    if ($category === '') {
        $errors[] = 'Выберите категорию.';
    } elseif (!in_array($category, $allowedCategories, true)) {
        $errors[] = 'Недопустимое значение категории.';
    }

    $allowedDifficulties = array_keys(getDifficultyLabels());

    if ($difficulty === '') {
        $errors[] = 'Выберите сложность.';
    } elseif (!in_array($difficulty, $allowedDifficulties, true)) {
        $errors[] = 'Недопустимое значение сложности.';
    }

    if ($startDate === '') {
        $errors[] = 'Укажите дату начала.';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $startDate);

        if (!$date || $date->format('Y-m-d') !== $startDate) {
            $errors[] = 'Дата начала имеет неверный формат.';
        }
    }

    if ($goalDays === '') {
        $errors[] = 'Укажите цель в днях.';
    } elseif (!ctype_digit($goalDays) || (int)$goalDays < 1 || (int)$goalDays > 365) {
        $errors[] = 'Цель должна быть числом от 1 до 365.';
    }

    $allowedDays = array_keys(getDayLabels());
    $days = array_values(array_filter($days, fn($d) => in_array($d, $allowedDays, true)));

    if (count($days) === 0) {
        $errors[] = 'Выберите хотя бы один день выполнения.';
    }

    $record = [
        'id' => uniqid('', true),
        'name' => $name,
        'description' => $description,
        'category' => $category,
        'difficulty' => $difficulty,
        'start_date' => $startDate,
        'goal_days' => (int)$goalDays,
        'days' => $days,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    return [$errors, $record];
}