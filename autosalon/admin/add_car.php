<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/validation.php';

requireAdmin();

$pdo = getDb();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateCar($_POST);

    if (!$errors) {
        $stmt = $pdo->prepare("
            INSERT INTO cars (brand, model, year, price, fuel_type, transmission, description, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            trim($_POST['brand']),
            trim($_POST['model']),
            (int)$_POST['year'],
            (float)$_POST['price'],
            $_POST['fuel_type'],
            $_POST['transmission'],
            trim($_POST['description']),
            date('Y-m-d H:i:s')
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
    <title>Добавить автомобиль</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main>
    <h1>Добавить автомобиль</h1>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="post" class="form">
        <input type="text" name="brand" required placeholder="Марка">
        <input type="text" name="model" required placeholder="Модель">
        <input type="number" name="year" required min="1980" max="<?= date('Y') ?>" placeholder="Год выпуска">
        <input type="number" name="price" required min="1" step="0.01" placeholder="Цена">

        <select name="fuel_type" required>
            <option value="">Выберите топливо</option>
            <option value="Petrol">Бензин</option>
            <option value="Diesel">Дизель</option>
            <option value="Hybrid">Гибрид</option>
            <option value="Electric">Электро</option>
        </select>

        <select name="transmission" required>
            <option value="">Выберите КПП</option>
            <option value="Manual">Механика</option>
            <option value="Automatic">Автомат</option>
        </select>

        <textarea name="description" required placeholder="Описание"></textarea>

        <button type="submit">Добавить</button>
    </form>

    <p><a href="cars.php">Назад</a></p>
</main>

</body>
</html>