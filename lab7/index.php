<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


require_once __DIR__ . '/src/functions.php';

$view = $_GET['view'] ?? 'native';
$page = $_GET['page'] ?? 'form';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/src/handler.php';
    exit;
}

if ($view === 'twig') {
    require_once __DIR__ . '/vendor/autoload.php';

    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/twig_templates');
    $twig = new \Twig\Environment($loader);

    require_once __DIR__ . '/src/TwigExtensions.php';
    $twig->addExtension(new \App\TwigExtensions());

    if ($page === 'list') {
        $sort = getSortField();
        $order = getSortOrder();

        $records = loadRecords();
        sortRecords($records, $sort, $order);

        echo $twig->render('list.twig', [
            'records' => $records,
            'sort' => $sort,
            'order' => $order,
            'total' => count($records),
            'cats_count' => count(array_unique(array_column($records, 'category'))),
            'avg_goal' => getAverageGoal($records),
            'category_labels' => getCategoryLabels(),
            'difficulty_labels' => getDifficultyLabels(),
            'difficulty_colors' => getDifficultyColors(),
            'day_labels' => getDayLabels(),
        ]);
    } else {
        echo $twig->render('form.twig', [
            'category_labels' => getCategoryLabels(),
            'difficulty_labels' => getDifficultyLabels(),
            'day_labels' => getDayLabels(),
        ]);
    }

    exit;
}

if ($page === 'list') {
    $sort = getSortField();
    $order = getSortOrder();

    $records = loadRecords();
    sortRecords($records, $sort, $order);

    $total = count($records);
    $cats_count = count(array_unique(array_column($records, 'category')));
    $avg_goal = getAverageGoal($records);

    $category_labels = getCategoryLabels();
    $difficulty_labels = getDifficultyLabels();
    $difficulty_colors = getDifficultyColors();
    $day_labels = getDayLabels();

    $title = 'Все привычки — Трекер привычек';
    $subtitle = 'Все сохранённые привычки';
    $content = __DIR__ . '/templates/list.php';

    require __DIR__ . '/templates/layout.php';
} else {
    $category_labels = getCategoryLabels();
    $difficulty_labels = getDifficultyLabels();
    $day_labels = getDayLabels();

    $title = 'Добавить привычку — Трекер привычек';
    $subtitle = 'Форма добавления новой привычки';
    $content = __DIR__ . '/templates/form.php';

    require __DIR__ . '/templates/layout.php';
}