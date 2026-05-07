<?php

declare(strict_types=1);

require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Удаление разрешено только через POST.');
}

checkCsrf();

$id = (int)($_POST['id'] ?? 0);
$view = $_POST['view'] ?? 'native';

if ($id > 0) {
    deleteRecord($id);
}

header('Location: index.php?page=list&view=' . urlencode($view));
exit;