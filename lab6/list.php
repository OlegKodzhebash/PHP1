<?php
// list.php — вывод всех привычек с сортировкой

header('Content-Type: text/html; charset=utf-8');

$file = __DIR__ . '/data.json';
$records = [];

if (file_exists($file)) {
    $raw = file_get_contents($file);
    $records = json_decode($raw, true) ?: [];
}

// --- Сортировка ---
$allowed_sort = ['name', 'category', 'difficulty', 'start_date', 'goal_days', 'created_at'];
$sort = in_array($_GET['sort'] ?? '', $allowed_sort) ? $_GET['sort'] : 'created_at';
$order = ($_GET['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

usort($records, function($a, $b) use ($sort, $order) {
    $va = $a[$sort] ?? '';
    $vb = $b[$sort] ?? '';

    if ($sort === 'goal_days') {
        $cmp = (int)$va <=> (int)$vb;
    } else {
        $cmp = strcmp((string)$va, (string)$vb);
    }

    return $order === 'asc' ? $cmp : -$cmp;
});

$category_labels = [
    'health'       => '🏃 Здоровье',
    'sport'        => '💪 Спорт',
    'education'    => '📚 Образование',
    'productivity' => '⚡ Продуктивность',
    'mindfulness'  => '🧘 Осознанность',
    'social'       => '🤝 Социальное',
    'finance'      => '💰 Финансы',
    'other'        => '🔖 Другое',
];

$difficulty_labels = [
    'easy'   => '🟢 Лёгкая',
    'medium' => '🟡 Средняя',
    'hard'   => '🔴 Сложная',
];

$difficulty_colors = [
    'easy'   => '#d4efdf',
    'medium' => '#fef9e7',
    'hard'   => '#fadbd8',
];

$day_labels = [
    'mon' => 'Пн',
    'tue' => 'Вт',
    'wed' => 'Ср',
    'thu' => 'Чт',
    'fri' => 'Пт',
    'sat' => 'Сб',
    'sun' => 'Вс',
];

// Функция построения URL сортировки
function sort_url(string $field, string $current_sort, string $current_order): string {
    $new_order = ($field === $current_sort && $current_order === 'asc') ? 'desc' : 'asc';
    return '?sort=' . urlencode($field) . '&order=' . $new_order;
}

function sort_arrow(string $field, string $current_sort, string $current_order): string {
    if ($field !== $current_sort) {
        return '<span style="color:#ccc">↕</span>';
    }
    return $current_order === 'asc' ? '▲' : '▼';
}

// Без strimwidth и без mbstring
function safe_trim_text(string $text, int $max = 80, string $suffix = '…'): string {
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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все привычки — Трекер привычек</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f4f8;
            color: #333;
            padding: 30px 15px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 8px;
            font-size: 2rem;
            color: #2c3e50;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        nav {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
            justify-content: center;
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

        nav a:hover,
        nav a.active {
            background: #3498db;
            color: #fff;
        }

        .stats {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 16px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            flex: 1;
            min-width: 140px;
            text-align: center;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 800;
            color: #3498db;
        }

        .stat-card .label {
            font-size: 0.82rem;
            color: #888;
            margin-top: 2px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .empty {
            text-align: center;
            padding: 48px 0;
            color: #aaa;
            font-size: 1.05rem;
        }

        .empty a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 700px;
        }

        thead tr {
            background: #f0f4f8;
        }

        th {
            padding: 12px 14px;
            text-align: left;
            font-weight: 700;
            color: #555;
            white-space: nowrap;
            border-bottom: 2px solid #dde3ea;
        }

        th a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        th a:hover {
            color: #2980b9;
        }

        th.active {
            color: #2980b9;
        }

        td {
            padding: 11px 14px;
            border-bottom: 1px solid #eef0f2;
            vertical-align: top;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #f8fbff;
        }

        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 600;
            margin: 2px 2px 2px 0;
        }

        .badge-day {
            background: #eaf0fb;
            color: #2471a3;
        }

        .badge-cat {
            background: #eafaf1;
            color: #1e8449;
        }

        .diff-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .name-cell {
            font-weight: 600;
            color: #2c3e50;
            max-width: 200px;
        }

        .desc-cell {
            color: #666;
            max-width: 220px;
            font-size: 0.85rem;
        }

        .date-cell {
            white-space: nowrap;
            color: #555;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Трекер привычек</h1>
    <p class="subtitle">Все сохранённые привычки</p>

    <nav>
        <a href="index.html">➕ Добавить</a>
        <a href="list.php" class="active">📊 Все привычки</a>
    </nav>

    <?php
    $total = count($records);
    $cats = array_unique(array_column($records, 'category'));
    $avg_goal = $total > 0 ? round(array_sum(array_column($records, 'goal_days')) / $total) : 0;
    ?>

    <div class="stats">
        <div class="stat-card">
            <div class="number"><?= $total ?></div>
            <div class="label">Всего привычек</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= count($cats) ?></div>
            <div class="label">Категорий</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= $avg_goal ?></div>
            <div class="label">Средняя цель (дней)</div>
        </div>
    </div>

    <div class="card">
        <?php if (empty($records)): ?>
            <div class="empty">
                <p>Привычек пока нет.</p>
                <p style="margin-top:10px"><a href="index.html">➕ Добавить первую привычку</a></p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th class="<?= $sort === 'name' ? 'active' : '' ?>">
                            <a href="<?= sort_url('name', $sort, $order) ?>">
                                Название <?= sort_arrow('name', $sort, $order) ?>
                            </a>
                        </th>
                        <th class="<?= $sort === 'category' ? 'active' : '' ?>">
                            <a href="<?= sort_url('category', $sort, $order) ?>">
                                Категория <?= sort_arrow('category', $sort, $order) ?>
                            </a>
                        </th>
                        <th class="<?= $sort === 'difficulty' ? 'active' : '' ?>">
                            <a href="<?= sort_url('difficulty', $sort, $order) ?>">
                                Сложность <?= sort_arrow('difficulty', $sort, $order) ?>
                            </a>
                        </th>
                        <th class="<?= $sort === 'start_date' ? 'active' : '' ?>">
                            <a href="<?= sort_url('start_date', $sort, $order) ?>">
                                Дата начала <?= sort_arrow('start_date', $sort, $order) ?>
                            </a>
                        </th>
                        <th class="<?= $sort === 'goal_days' ? 'active' : '' ?>">
                            <a href="<?= sort_url('goal_days', $sort, $order) ?>">
                                Цель (дн.) <?= sort_arrow('goal_days', $sort, $order) ?>
                            </a>
                        </th>
                        <th>Дни</th>
                        <th>Описание</th>
                        <th class="<?= $sort === 'created_at' ? 'active' : '' ?>">
                            <a href="<?= sort_url('created_at', $sort, $order) ?>">
                                Добавлено <?= sort_arrow('created_at', $sort, $order) ?>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="name-cell"><?= htmlspecialchars($r['name'] ?? '') ?></td>
                            <td>
                                <span class="badge badge-cat">
                                    <?= htmlspecialchars($category_labels[$r['category'] ?? ''] ?? ($r['category'] ?? '')) ?>
                                </span>
                            </td>
                            <td>
                                <?php $diff = $r['difficulty'] ?? ''; ?>
                                <span class="diff-badge" style="background:<?= htmlspecialchars($difficulty_colors[$diff] ?? '#eee') ?>">
                                    <?= htmlspecialchars($difficulty_labels[$diff] ?? $diff) ?>
                                </span>
                            </td>
                            <td class="date-cell"><?= htmlspecialchars($r['start_date'] ?? '') ?></td>
                            <td style="text-align:center; font-weight:600"><?= (int)($r['goal_days'] ?? 0) ?></td>
                            <td>
                                <?php foreach (($r['days'] ?? []) as $d): ?>
                                    <span class="badge badge-day"><?= htmlspecialchars($day_labels[$d] ?? $d) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td class="desc-cell">
                                <?= nl2br(htmlspecialchars(safe_trim_text($r['description'] ?? '', 80, '…'))) ?>
                            </td>
                            <td class="date-cell" style="font-size:0.82rem; color:#999">
                                <?= htmlspecialchars($r['created_at'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>