<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

requireAdmin();

$pdo = getDb();

$errors = [];
$success = '';

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
        $errors[] = 'Пароль должен быть минимум 6 символов.';
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
                'admin',
                date('Y-m-d H:i:s')
            ]);

            $success = 'Администратор успешно создан.';
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
    <title>Создать администратора</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main>
    <h1>Создать администратора</h1>

    <?php if ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="post" class="form">
        <input type="text" name="name" required placeholder="Имя">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required minlength="6" placeholder="Пароль">
        <button type="submit">Создать</button>
    </form>

    <p><a href="index.php">Назад</a></p>
</main>

</body>
</html>