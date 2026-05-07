<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/auth.php';

requireAdmin();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <h1>Админ-панель</h1>
    <nav>
        <a href="../index.php">На сайт</a>
        <a href="cars.php">Автомобили</a>
        <a href="add_car.php">Добавить авто</a>
        <a href="users.php">Пользователи</a>
        <a href="create_admin.php">Создать администратора</a>
        <a href="../logout.php">Выход</a>
    </nav>
</header>

<main>
    <h2>Функции администратора</h2>

    <ul>
        <li>Просмотр автомобилей</li>
        <li>Добавление автомобилей</li>
        <li>Редактирование автомобилей</li>
        <li>Удаление автомобилей</li>
        <li>Просмотр пользователей</li>
        <li>Создание новых администраторов</li>
    </ul>
</main>

</body>
</html>