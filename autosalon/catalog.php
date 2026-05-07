<?php
require_once __DIR__ . '/src/init.php';
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/auth.php';

$pdo = getDb();
$stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог автомобилей</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Каталог автомобилей</h1>
    <nav>
        <a href="index.php">Главная</a>
        <a href="search.php">Поиск</a>
        <?php if (isLoggedIn()): ?>
            <a href="profile.php">Профиль</a>
            <a href="logout.php">Выход</a>
        <?php else: ?>
            <a href="login.php">Вход</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <div class="cards">
        <?php foreach ($cars as $car): ?>
            <div class="card">
                <h3><?= e($car['brand']) ?> <?= e($car['model']) ?></h3>
                <p>Год: <?= e($car['year']) ?></p>
                <p>Цена: <?= e($car['price']) ?> €</p>
                <p>Топливо: <?= e($car['fuel_type']) ?></p>
                <p>КПП: <?= e($car['transmission']) ?></p>
                <a href="car.php?id=<?= $car['id'] ?>">Подробнее</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>