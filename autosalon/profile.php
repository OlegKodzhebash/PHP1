<?php
require_once __DIR__ . '/src/init.php';
require_once __DIR__ . '/src/auth.php';

requireLogin();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Личный кабинет</h1>
    <nav>
        <a href="index.php">Главная</a>
        <a href="catalog.php">Каталог</a>
        <?php if (isAdmin()): ?>
            <a href="admin/index.php">Админ-панель</a>
        <?php endif; ?>
        <a href="logout.php">Выход</a>
    </nav>
</header>

<main>
    <h2>Данные пользователя</h2>
    <p>Имя: <?= e($_SESSION['user']['name']) ?></p>
    <p>Email: <?= e($_SESSION['user']['email']) ?></p>
    <p>Роль: <?= e($_SESSION['user']['role']) ?></p>
</main>

</body>
</html>