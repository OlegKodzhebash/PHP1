<?php

header('Content-Type: text/html; charset=utf-8');

$errors = [];
$data = [];

// --- Принимаем данные из $_POST ---
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category    = trim($_POST['category'] ?? '');
$difficulty  = trim($_POST['difficulty'] ?? '');
$start_date  = trim($_POST['start_date'] ?? '');
$goal_days   = trim($_POST['goal_days'] ?? '');
$days        = $_POST['days'] ?? [];

// --- Валидация ---

// name
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

// category — enum
$allowed_categories = ['health','sport','education','productivity','mindfulness','social','finance','other'];
if ($category === '') {
    $errors[] = 'Выберите категорию.';
} elseif (!in_array($category, $allowed_categories, true)) {
    $errors[] = 'Недопустимое значение категории.';
}

// difficulty — enum
$allowed_difficulties = ['easy','medium','hard'];
if ($difficulty === '') {
    $errors[] = 'Выберите сложность.';
} elseif (!in_array($difficulty, $allowed_difficulties, true)) {
    $errors[] = 'Недопустимое значение сложности.';
}

// start_date — формат даты YYYY-MM-DD
if ($start_date === '') {
    $errors[] = 'Укажите дату начала.';
} else {
    $d = DateTime::createFromFormat('Y-m-d', $start_date);
    if (!$d || $d->format('Y-m-d') !== $start_date) {
        $errors[] = 'Дата начала имеет неверный формат.';
    }
}

// goal_days — число
if ($goal_days === '') {
    $errors[] = 'Укажите цель в днях.';
} elseif (!ctype_digit($goal_days) || (int)$goal_days < 1 || (int)$goal_days > 365) {
    $errors[] = 'Цель должна быть числом от 1 до 365.';
}

// days — checkbox, хотя бы один
$allowed_days = ['mon','tue','wed','thu','fri','sat','sun'];
$days = array_filter($days, fn($d) => in_array($d, $allowed_days, true));
$days = array_values($days);
if (count($days) === 0) {
    $errors[] = 'Выберите хотя бы один день выполнения.';
}


if (empty($errors)) {
    $record = [
        'id'          => uniqid('', true),
        'name'        => $name,
        'description' => $description,
        'category'    => $category,
        'difficulty'  => $difficulty,
        'start_date'  => $start_date,
        'goal_days'   => (int)$goal_days,
        'days'        => $days,
        'created_at'  => date('Y-m-d H:i:s'),
    ];

    $file = __DIR__ . '/data.json';

    // Читаем существующие данные
    $existing = [];
    if (file_exists($file)) {
        $raw = file_get_contents($file);
        $existing = json_decode($raw, true) ?: [];
    }

    // Добавляем новую запись
    $existing[] = $record;

    // Сохраняем обратно
    $result = file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if ($result === false) {
        $errors[] = 'Ошибка записи в файл. Проверьте права доступа.';
    }
}

// --- Вывод результата ---
$category_labels = [
    'health'        => '🏃 Здоровье',
    'sport'         => '💪 Спорт',
    'education'     => '📚 Образование',
    'productivity'  => '⚡ Продуктивность',
    'mindfulness'   => '🧘 Осознанность',
    'social'        => '🤝 Социальное',
    'finance'       => '💰 Финансы',
    'other'         => '🔖 Другое',
];
$difficulty_labels = [
    'easy'   => '🟢 Лёгкая',
    'medium' => '🟡 Средняя',
    'hard'   => '🔴 Сложная',
];
$day_labels = [
    'mon'=>'Пн','tue'=>'Вт','wed'=>'Ср',
    'thu'=>'Чт','fri'=>'Пт','sat'=>'Сб','sun'=>'Вс',
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Трекер привычек</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f4f8;
            color: #333;
            padding: 30px 15px;
        }
        .container { max-width: 700px; margin: 0 auto; }
        h1 { text-align: center; margin-bottom: 8px; font-size: 2rem; color: #2c3e50; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 0.95rem; }
        nav {
            display: flex; gap: 12px; margin-bottom: 28px; justify-content: center;
        }
        nav a {
            padding: 9px 22px;
            background: #fff;
            border: 2px solid #3498db;
            color: #3498db;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        nav a:hover { background: #3498db; color: #fff; }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .success-icon { font-size: 3rem; text-align: center; margin-bottom: 16px; }
        .success-title { text-align: center; font-size: 1.4rem; font-weight: 700; color: #27ae60; margin-bottom: 20px; }
        .error-title { text-align: center; font-size: 1.4rem; font-weight: 700; color: #e74c3c; margin-bottom: 16px; }
        .error-list {
            background: #fdf0ef;
            border: 1.5px solid #e74c3c;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }
        .error-list li { color: #c0392b; margin-bottom: 5px; margin-left: 18px; font-size: 0.93rem; }
        .record-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .record-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #eef0f2;
            font-size: 0.93rem;
        }
        .record-table td:first-child {
            font-weight: 600;
            color: #555;
            width: 40%;
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 600;
            background: #ebf5fb;
            color: #2980b9;
        }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn {
            flex: 1;
            padding: 11px;
            border-radius: 7px;
            text-align: center;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-primary:hover { background: #2176ae; }
        .btn-outline { background: #fff; color: #3498db; border: 2px solid #3498db; }
        .btn-outline:hover { background: #3498db; color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Трекер привычек</h1>
    <p class="subtitle">Результат сохранения</p>

    <nav>
        <a href="index.html">➕ Добавить</a>
        <a href="list.php">📊 Все привычки</a>
    </nav>

    <div class="card">
        <?php if (!empty($errors)): ?>
            <p class="error-title">❌ Ошибки валидации</p>
            <ul class="error-list">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="actions">
                <a href="javascript:history.back()" class="btn btn-primary">← Вернуться к форме</a>
            </div>
        <?php else: ?>
            <div class="success-icon">✅</div>
            <p class="success-title">Привычка сохранена!</p>
            <table class="record-table">
                <tr><td>Название</td><td><?= htmlspecialchars($record['name']) ?></td></tr>
                <tr><td>Описание</td><td><?= nl2br(htmlspecialchars($record['description'] ?: '—')) ?></td></tr>
                <tr><td>Категория</td><td><?= htmlspecialchars($category_labels[$record['category']] ?? $record['category']) ?></td></tr>
                <tr><td>Сложность</td><td><?= htmlspecialchars($difficulty_labels[$record['difficulty']] ?? $record['difficulty']) ?></td></tr>
                <tr><td>Дата начала</td><td><?= htmlspecialchars($record['start_date']) ?></td></tr>
                <tr><td>Цель</td><td><?= (int)$record['goal_days'] ?> дней</td></tr>
                <tr>
                    <td>Дни выполнения</td>
                    <td>
                        <?php foreach ($record['days'] as $d): ?>
                            <span class="badge"><?= $day_labels[$d] ?? $d ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <tr><td>Дата добавления</td><td><?= htmlspecialchars($record['created_at']) ?></td></tr>
            </table>
            <div class="actions">
                <a href="index.html" class="btn btn-outline">➕ Добавить ещё</a>
                <a href="list.php" class="btn btn-primary">📊 Все привычки</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
