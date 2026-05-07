<?php
require_once __DIR__ . '/src/init.php';
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/src/auth.php';

$pdo = getDb();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        header('Location: profile.php');
        exit;
    } else {
        $error = 'Неверный email или пароль.';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<main>
    <h1>Вход</h1>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post" class="form">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Пароль">
        <button type="submit">Войти</button>
    </form>

    <p><a href="register.php">Создать аккаунт</a></p>

    <p>Админ по умолчанию:</p>
    <p>Email: <b>admin@example.com</b></p>
    <p>Пароль: <b>admin123</b></p>
</main>

</body>
</html>