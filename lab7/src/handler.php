<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$view = $_GET['view'] ?? 'native';

[$errors, $record] = validateHabit($_POST);

if (empty($errors)) {
    $records = loadRecords();
    $records[] = $record;

    if (!saveRecords($records)) {
        $errors[] = 'Ошибка записи в файл. Проверьте права доступа.';
    }
}

$category_labels = getCategoryLabels();
$difficulty_labels = getDifficultyLabels();
$day_labels = getDayLabels();

if ($view === 'twig') {
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/TwigExtensions.php';

    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../twig_templates');
    $twig = new \Twig\Environment($loader);
    $twig->addExtension(new \App\TwigExtensions());

    echo $twig->render('result.twig', [
        'errors' => $errors,
        'record' => $record,
        'category_labels' => $category_labels,
        'difficulty_labels' => $difficulty_labels,
        'day_labels' => $day_labels,
    ]);

    exit;
}

$title = 'Результат сохранения — Трекер привычек';
$subtitle = 'Результат сохранения';
$content = __DIR__ . '/../templates/result.php';

require __DIR__ . '/../templates/layout.php';