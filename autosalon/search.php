<?php
require_once __DIR__ . '/src/init.php';
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/auth.php';

$pdo = getDb();

$brand = trim($_GET['brand'] ?? '');
$fuel = trim($_GET['fuel_type'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');

$sql = "SELECT * FROM cars WHERE 1=1";
$params = [];

if ($brand !== '') {
    $sql .= " AND brand LIKE ?";
    $params[] = '%' . $brand . '%';
}

if ($fuel !== '') {
    $sql .= " AND fuel_type = ?";
    $params[] = $fuel;
}

if ($minPrice !== '') {
    $sql .= " AND price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice !== '') {
    $sql .= " AND price <= ?";
    $params[] = $maxPrice;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поиск автомобилей</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Поиск автомобилей</h1>
    <nav>
        <a href="index.php">Главная</a>
        <a href="catalog.php">Каталог</a>
    </nav>
</header>

<main>
    <form method="get" class="form">
        <input type="text" name="brand" placeholder="Марка" value="<?= e($brand) ?>">

        <select name="fuel_type">
            <option value="">Любое топливо</option>
            <option value="Petrol" <?= $fuel === 'Petrol' ? 'selected' : '' ?>>Бензин</option>
            <option value="Diesel" <?= $fuel === 'Diesel' ? 'selected' : '' ?>>Дизель</option>
            <option value="Hybrid" <?= $fuel === 'Hybrid' ? 'selected' : '' ?>>Гибрид</option>
            <option value="Electric" <?= $fuel === 'Electric' ? 'selected' : '' ?>>Электро</option>
        </select>

        <input type="number" name="min_price" placeholder="Цена от" value="<?= e($minPrice) ?>">
        <input type="number" name="max_price" placeholder="Цена до" value="<?= e($maxPrice) ?>">

        <button type="submit">Искать</button>
    </form>

    <h2>Результаты поиска</h2>

    <div class="cards">
        <?php foreach ($cars as $car): ?>
            <div class="card">
                <h3><?= e($car['brand']) ?> <?= e($car['model']) ?></h3>
                <p>Год: <?= e($car['year']) ?></p>
                <p>Цена: <?= e($car['price']) ?> €</p>
                <a href="car.php?id=<?= $car['id'] ?>">Подробнее</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>