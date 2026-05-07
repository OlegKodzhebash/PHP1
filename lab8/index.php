<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/csrf.php';

use App\TwigExtensions;

$view = $_GET['view'] ?? 'native';
$page = $_GET['page'] ?? 'create';

$errors = [];
$categories = getCategories();
$difficulties = getDifficulties();
$dayLabels = getDayLabels();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    [$errors, $data] = validateHabit($_POST);

    if (empty($errors)) {
        createRecord($data);
        header('Location: index.php?page=list&view=' . urlencode($view));
        exit;
    }
}

if ($page === 'list') {
    $query = trim($_GET['q'] ?? '');

    $records = $query !== ''
        ? searchRecords($query)
        : getAllRecords();

    if ($view === 'twig') {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/twig_templates');
        $twig = new \Twig\Environment($loader);
        $twig->addExtension(new TwigExtensions());

        echo $twig->render('list.twig', [
            'records' => $records,
            'dayLabels' => $dayLabels,
            'difficulties' => $difficulties,
            'query' => $query,
            'csrf_token' => csrfToken(),
        ]);
        exit;
    }

    $title = 'Список привычек';
    $content = __DIR__ . '/templates/list.php';
    require __DIR__ . '/templates/layout.php';
    exit;
}

$record = null;

if ($view === 'twig') {
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/twig_templates');
    $twig = new \Twig\Environment($loader);
    $twig->addExtension(new TwigExtensions());

    echo $twig->render('form.twig', [
        'record' => $record,
        'errors' => $errors,
        'categories' => $categories,
        'difficulties' => $difficulties,
        'dayLabels' => $dayLabels,
        'csrf_token' => csrfToken(),
        'action' => 'index.php?view=twig',
    ]);
    exit;
}

$title = 'Добавить привычку';
$content = __DIR__ . '/templates/form.php';

require __DIR__ . '/templates/layout.php';