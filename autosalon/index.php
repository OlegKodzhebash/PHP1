<?php
require_once __DIR__ . '/src/init.php';
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/auth.php';

$pdo = getDb();

$stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC LIMIT 3");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>АИС Автосалон</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>АИС Автосалон</h1>
    <nav>
        <a href="index.php">Главная</a>
        <a href="catalog.php">Каталог</a>
        <a href="search.php">Поиск</a>

        <?php if (isLoggedIn()): ?>
            <a href="profile.php">Профиль</a>
            <?php if (isAdmin()): ?>
                <a href="admin/index.php">Админ-панель</a>
            <?php endif; ?>
            <a href="logout.php">Выход</a>
        <?php else: ?>
            <a href="login.php">Вход</a>
            <a href="register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <h2>Добро пожаловать в автосалон</h2>
    <p>На сайте можно просматривать автомобили, искать их по параметрам и оставлять заявки.</p>

    <h2>Последние автомобили</h2>

    <div class="cards">
        <?php foreach ($cars as $car): ?>
            <div class="card">
                <h3><?= e($car['brand']) ?> <?= e($car['model']) ?></h3>
                <p>Год: <?= e($car['year']) ?></p>
                <p>Цена: <?= e($car['price']) ?> €</p>
                <p>Топливо: <?= e($car['fuel_type']) ?></p>
                <a href="car.php?id=<?= $car['id'] ?>">Подробнее</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>