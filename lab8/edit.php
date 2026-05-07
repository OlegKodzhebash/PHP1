<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/csrf.php';

use App\TwigExtensions;

$view = $_GET['view'] ?? 'native';
$id = (int)($_GET['id'] ?? 0);

$record = getRecordById($id);

if (!$record) {
    die('Запись не найдена.');
}

$errors = [];
$categories = getCategories();
$difficulties = getDifficulties();
$dayLabels = getDayLabels();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();

    [$errors, $data] = validateHabit($_POST);

    if (empty($errors)) {
        updateRecord($id, $data);
        header('Location: index.php?page=list&view=' . urlencode($view));
        exit;
    }
}

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
        'action' => 'edit.php?id=' . $id . '&view=twig',
    ]);
    exit;
}

$title = 'Редактировать привычку';
$content = __DIR__ . '/templates/form.php';

require __DIR__ . '/templates/layout.php';