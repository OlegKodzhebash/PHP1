<?php
require_once __DIR__ . '/src/init.php';
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/auth.php';

$pdo = getDb();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') {
        $errors[] = 'Введите имя.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов.';
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password_hash, role, created_at)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                'user',
                date('Y-m-d H:i:s')
            ]);

            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Пользователь с таким email уже существует.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<main>
    <h1>Регистрация</h1>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="post" class="form">
        <input type="text" name="name" required placeholder="Имя">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required minlength="6" placeholder="Пароль">
        <button type="submit">Зарегистрироваться</button>
    </form>

    <p><a href="login.php">Уже есть аккаунт?</a></p>
</main>

</body>
</html>