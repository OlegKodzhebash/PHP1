<?php
require_once __DIR__ . '/src/init.php';
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/auth.php';

$pdo = getDb();

$id = $_GET['id'] ?? null;

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    die('Некорректный ID автомобиля.');
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    die('Автомобиль не найден.');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin();

    $message = trim($_POST['message'] ?? '');

    if ($message === '') {
        $error = 'Введите сообщение.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO requests (user_id, car_id, message, created_at)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['user']['id'],
            $car['id'],
            $message,
            date('Y-m-d H:i:s')
        ]);

        $success = 'Заявка успешно отправлена.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Автомобиль</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1><?= e($car['brand']) ?> <?= e($car['model']) ?></h1>
    <nav>
        <a href="index.php">Главная</a>
        <a href="catalog.php">Каталог</a>
    </nav>
</header>

<main>
    <div class="card">
        <p>Год: <?= e($car['year']) ?></p>
        <p>Цена: <?= e($car['price']) ?> €</p>
        <p>Тип топлива: <?= e($car['fuel_type']) ?></p>
        <p>Коробка передач: <?= e($car['transmission']) ?></p>
        <p><?= e($car['description']) ?></p>
    </div>

    <h2>Оставить заявку</h2>

    <?php if ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if (isLoggedIn()): ?>
        <form method="post">
            <textarea name="message" required placeholder="Ваше сообщение"></textarea>
            <button type="submit">Отправить заявку</button>
        </form>
    <?php else: ?>
        <p>Чтобы оставить заявку, необходимо <a href="login.php">войти</a>.</p>
    <?php endif; ?>
</main>

</body>
</html>