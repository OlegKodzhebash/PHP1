<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

requireAdmin();

$pdo = getDb();
$stmt = $pdo->query("SELECT * FROM cars ORDER BY id DESC");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление автомобилями</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <h1>Управление автомобилями</h1>
    <nav>
        <a href="index.php">Админ-панель</a>
        <a href="add_car.php">Добавить авто</a>
    </nav>
</header>

<main>
    <table>
        <tr>
            <th>ID</th>
            <th>Марка</th>
            <th>Модель</th>
            <th>Год</th>
            <th>Цена</th>
            <th>Действия</th>
        </tr>

        <?php foreach ($cars as $car): ?>
            <tr>
                <td><?= e($car['id']) ?></td>
                <td><?= e($car['brand']) ?></td>
                <td><?= e($car['model']) ?></td>
                <td><?= e($car['year']) ?></td>
                <td><?= e($car['price']) ?> €</td>
                <td>
                    <a href="edit_car.php?id=<?= $car['id'] ?>">Редактировать</a>
                    <a href="delete_car.php?id=<?= $car['id'] ?>" onclick="return confirm('Удалить автомобиль?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

</body>
</html>