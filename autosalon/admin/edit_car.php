<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/validation.php';

requireAdmin();

$pdo = getDb();

$id = $_GET['id'] ?? null;

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    die('Некорректный ID.');
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    die('Автомобиль не найден.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateCar($_POST);

    if (!$errors) {
        $stmt = $pdo->prepare("
            UPDATE cars
            SET brand = ?, model = ?, year = ?, price = ?, fuel_type = ?, transmission = ?, description = ?
            WHERE id = ?
        ");

        $stmt->execute([
            trim($_POST['brand']),
            trim($_POST['model']),
            (int)$_POST['year'],
            (float)$_POST['price'],
            $_POST['fuel_type'],
            $_POST['transmission'],
            trim($_POST['description']),
            $id
        ]);

        header('Location: cars.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать автомобиль</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main>
    <h1>Редактировать автомобиль</h1>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="post" class="form">
        <input type="text" name="brand" required value="<?= e($car['brand']) ?>">
        <input type="text" name="model" required value="<?= e($car['model']) ?>">
        <input type="number" name="year" required value="<?= e($car['year']) ?>">
        <input type="number" name="price" required step="0.01" value="<?= e($car['price']) ?>">

        <select name="fuel_type" required>
            <option value="Petrol" <?= $car['fuel_type'] === 'Petrol' ? 'selected' : '' ?>>Бензин</option>
            <option value="Diesel" <?= $car['fuel_type'] === 'Diesel' ? 'selected' : '' ?>>Дизель</option>
            <option value="Hybrid" <?= $car['fuel_type'] === 'Hybrid' ? 'selected' : '' ?>>Гибрид</option>
            <option value="Electric" <?= $car['fuel_type'] === 'Electric' ? 'selected' : '' ?>>Электро</option>
        </select>

        <select name="transmission" required>
            <option value="Manual" <?= $car['transmission'] === 'Manual' ? 'selected' : '' ?>>Механика</option>
            <option value="Automatic" <?= $car['transmission'] === 'Automatic' ? 'selected' : '' ?>>Автомат</option>
        </select>

        <textarea name="description" required><?= e($car['description']) ?></textarea>

        <button type="submit">Сохранить</button>
    </form>

    <p><a href="cars.php">Назад</a></p>
</main>

</body>
</html>